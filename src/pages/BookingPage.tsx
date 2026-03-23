import React, { useCallback, useEffect, useMemo, useState } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { ArrowLeft, ArrowRight } from 'lucide-react';
import { apiFetch, apiJson } from '../lib/api';
import { useAuth } from '../context/AuthContext';
import BookingDateCalendar from '../components/BookingDateCalendar';
import {
  SALON_LOCATION_ADDRESS,
  SALON_LOCATION_NAME,
  SALON_NAME,
  SALON_LOCATION_PHONE,
} from '../lib/salonVenue';
import { clearBookingDraft, consumeBookingDraft, persistBookingDraft } from '../lib/bookingDraftStorage';

interface Category {
  id: string;
  name: string;
  services: { name: string; price: string }[];
}

interface SlotCell {
  time: string;
  state: 'available' | 'booked' | 'past';
}

type Step = 'choose' | 'review' | 'success';

function formatYmdLong(ymd: string): string {
  const [y, m, d] = ymd.split('-').map((x) => parseInt(x, 10));
  if (!y || !m || !d) return ymd;
  const dt = new Date(y, m - 1, d);
  return Number.isNaN(dt.getTime())
    ? ymd
    : dt.toLocaleDateString('en-US', { weekday: 'short', month: 'long', day: 'numeric', year: 'numeric' });
}

function formatTimeHm(hm: string): string {
  const t = hm.trim().slice(0, 5);
  const [hh, mm] = t.split(':').map((x) => parseInt(x, 10));
  if (Number.isNaN(hh) || Number.isNaN(mm)) return hm;
  const d = new Date(2000, 0, 1, hh, mm, 0, 0);
  return d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
}

function ReviewSection({ label, children }: { label: string; children: React.ReactNode }) {
  return (
    <div className="py-4 border-b border-salon-ink/5 last:border-0 last:pb-0">
      <p className="text-[10px] uppercase tracking-[0.2em] text-salon-gold mb-2">{label}</p>
      <div className="text-salon-ink">{children}</div>
    </div>
  );
}

export default function BookingPage() {
  const navigate = useNavigate();
  const location = useLocation();
  const { user, loading: authLoading } = useAuth();

  const [categories, setCategories] = useState<Category[]>([]);
  const [slotsByDate, setSlotsByDate] = useState<Record<string, SlotCell[]>>({});
  const [metaLoading, setMetaLoading] = useState(true);
  const [metaError, setMetaError] = useState('');

  const [step, setStep] = useState<Step>('choose');
  /** Single pick: `categoryId|serviceName` */
  const [selectedService, setSelectedService] = useState<string | null>(null);
  const [date, setDate] = useState('');
  const [time, setTime] = useState('');
  const [guestName, setGuestName] = useState('');
  const [guestEmail, setGuestEmail] = useState('');
  const [guestPhone, setGuestPhone] = useState('');
  const [submitting, setSubmitting] = useState(false);
  const [msg, setMsg] = useState<{ ok: boolean; text: string; code?: string } | null>(null);

  const [successSnapshot, setSuccessSnapshot] = useState<{
    date: string;
    time: string;
    lines: string[];
    name: string;
    email: string;
    phone: string;
  } | null>(null);

  const isClientUser = user?.role === 'client';

  const persistDraftBeforeSignIn = useCallback(() => {
    const stepToSave = step === 'review' ? 'review' : 'choose';
    persistBookingDraft({
      v: 1,
      step: stepToSave,
      selectedService,
      date,
      time,
      guestName,
      guestEmail,
      guestPhone,
    });
  }, [step, selectedService, date, time, guestName, guestEmail, guestPhone]);

  const availableDates = useMemo(() => {
    return Object.keys(slotsByDate)
      .filter((d) => (slotsByDate[d] ?? []).some((c) => c.state === 'available'))
      .sort();
  }, [slotsByDate]);

  useEffect(() => {
    void (async () => {
      setMetaLoading(true);
      setMetaError('');
      try {
        const data = await apiJson<{
          ok: boolean;
          categories?: Category[];
          slotsByDate?: Record<string, SlotCell[]>;
        }>('/api/client/booking-meta.php', { method: 'GET' });
        const cats = data.categories ?? [];
        setCategories(cats);
        const s: Record<string, SlotCell[]> = data.slotsByDate ?? {};
        setSlotsByDate(s);
        const dates = Object.keys(s).sort();

        const draft = consumeBookingDraft();
        if (draft) {
          setGuestName(draft.guestName);
          setGuestEmail(draft.guestEmail);
          setGuestPhone(draft.guestPhone);

          const [cid, sname] = (draft.selectedService ?? '').split('|');
          const cat = cats.find((c) => c.id === cid);
          const serviceOk =
            Boolean(draft.selectedService) && Boolean(cat?.services.some((x) => x.name === sname));
          setSelectedService(serviceOk ? draft.selectedService : null);

          const nextDate = draft.date && s[draft.date] ? draft.date : dates[0] || '';
          setDate(nextDate);
          const cells = s[nextDate] ?? [];
          const nextTime =
            draft.time && cells.some((c) => c.time === draft.time && c.state === 'available') ? draft.time : '';
          setTime(nextTime);

          const canReview =
            draft.step === 'review' && serviceOk && Boolean(nextDate) && Boolean(nextTime);
          setStep(canReview ? 'review' : 'choose');
        } else {
          setDate((prev) => (prev && s[prev] ? prev : dates[0] || ''));
        }
      } catch {
        setMetaError('Could not load booking options.');
      } finally {
        setMetaLoading(false);
      }
    })();
  }, []);

  const preselect = (location.state as { preselectCategory?: string } | null)?.preselectCategory;
  useEffect(() => {
    if (!preselect || categories.length === 0) return;
    const el = document.getElementById(`booking-cat-${preselect}`);
    el?.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }, [preselect, categories]);

  const pickService = (catId: string, svcName: string) => {
    const key = `${catId}|${svcName}`;
    setSelectedService((prev) => (prev === key ? null : key));
  };

  const timesForDate = date ? slotsByDate[date] ?? [] : [];

  useEffect(() => {
    if (time && !timesForDate.some((c) => c.time === time && c.state === 'available')) {
      setTime('');
    }
  }, [date, time, timesForDate]);

  const serviceSummaryLines = useMemo(() => {
    if (!selectedService) return [];
    const [catId, svcName] = selectedService.split('|');
    const cat = categories.find((c) => c.id === catId);
    return cat ? [`${cat.name} — ${svcName}`] : [];
  }, [selectedService, categories]);

  const snap = successSnapshot;
  const displayDate = snap?.date ?? date;
  const displayTime = snap?.time ?? time;
  const displayLines = snap?.lines ?? serviceSummaryLines;
  const customerDisplayName = snap
    ? snap.name
    : isClientUser
      ? (user?.name ?? '')
      : guestName.trim();
  const customerDisplayEmail = snap ? snap.email : isClientUser ? (user?.email ?? '') : guestEmail.trim();
  const customerPhoneDisplay = snap ? snap.phone : guestPhone.trim();

  const goReview = () => {
    setMsg(null);
    if (!selectedService || !date || !time) {
      setMsg({ ok: false, text: 'Choose one service, a date, and a time.' });
      return;
    }
    setStep('review');
  };

  const onSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setMsg(null);
    if (!selectedService || !date || !time) {
      setMsg({ ok: false, text: 'Choose a service, date, and time.' });
      return;
    }
    if (!isClientUser) {
      if (!guestName.trim() || !guestEmail.trim()) {
        setMsg({ ok: false, text: 'Please enter your name and email so we can confirm your visit.' });
        return;
      }
    }
    setSubmitting(true);
    try {
      const payload: Record<string, unknown> = {
        booking_date: date,
        booking_time: time,
        services: [selectedService],
      };
      if (!isClientUser) {
        payload.guest_name = guestName.trim();
        payload.guest_email = guestEmail.trim();
        payload.guest_phone = guestPhone.trim();
      }
      const res = await apiFetch('/api/client/bookings.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });
      const data = (await res.json()) as {
        ok?: boolean;
        message?: string;
        error?: string;
        code?: string;
      };
      if (!res.ok || !data.ok) {
        setMsg({
          ok: false,
          text: data.error || 'Booking failed.',
          code: data.code,
        });
      } else {
        setMsg({ ok: true, text: data.message || 'Submitted.' });
        setSuccessSnapshot({
          date,
          time,
          lines: [...serviceSummaryLines],
          name: isClientUser ? (user?.name ?? '') : guestName.trim(),
          email: isClientUser ? (user?.email ?? '') : guestEmail.trim(),
          phone: guestPhone.trim(),
        });
        setStep('success');
        setSelectedService(null);
        clearBookingDraft();
      }
    } catch {
      setMsg({ ok: false, text: 'Network error.' });
    } finally {
      setSubmitting(false);
    }
  };

  if (authLoading || metaLoading) {
    return (
      <div className="min-h-screen bg-salon-beige pt-28 flex justify-center">
        <p className="text-salon-ink/60">Loading…</p>
      </div>
    );
  }

  const reviewBlock = (
    <div className="rounded-2xl border border-salon-ink/10 bg-white shadow-[0_20px_50px_-24px_rgba(0,0,0,0.15)] overflow-hidden">
      <div className="px-6 sm:px-8 py-5 bg-gradient-to-r from-salon-beige/80 to-white border-b border-salon-ink/5">
        <h2 className="text-xl sm:text-2xl font-serif text-salon-ink">Confirm your appointment</h2>
        <p className="text-sm text-salon-ink/55 mt-1">Review details — your time will be reserved when you submit.</p>
      </div>
      <div className="px-6 sm:px-8 pt-2 pb-10 sm:pb-12">
        <ReviewSection label="Service">
          <p className="font-medium">{displayLines[0] ?? '—'}</p>
        </ReviewSection>
        <ReviewSection label="Date & time">
          <p className="font-medium">
            {formatYmdLong(displayDate)} · {formatTimeHm(displayTime)}
          </p>
        </ReviewSection>
        <ReviewSection label="Salon">
          <p className="font-medium">{SALON_NAME}</p>
        </ReviewSection>
        <ReviewSection label="Location">
          <p className="font-medium">{SALON_LOCATION_NAME}</p>
          <p className="text-sm text-salon-ink/65 mt-1">{SALON_LOCATION_ADDRESS}</p>
          <p className="text-xs text-salon-ink/45 mt-2">Phone {SALON_LOCATION_PHONE}</p>
        </ReviewSection>
        <ReviewSection label={isClientUser ? 'Your account' : 'Guest contact'}>
          <p className="font-medium">{customerDisplayName || '—'}</p>
          <p className="text-sm text-salon-ink/65 mt-1">{customerDisplayEmail || '—'}</p>
          {!isClientUser && customerPhoneDisplay && <p className="text-sm text-salon-ink/65 mt-1">{customerPhoneDisplay}</p>}
        </ReviewSection>
      </div>
    </div>
  );

  return (
    <div className="min-h-screen bg-salon-beige pt-28 pb-20 px-6">
      <div className="max-w-2xl mx-auto">
        <h1 className="text-3xl font-serif text-salon-ink mb-2">Book appointment</h1>
        {isClientUser ? (
          <p className="text-sm text-salon-ink/60 mb-8">
            Signed in as <strong>{user!.name}</strong>.{' '}
            <Link to="/dashboard" className="text-salon-gold hover:underline">
              Dashboard
            </Link>
          </p>
        ) : (
          <p className="text-sm text-salon-ink/60 mb-8">
            Book as a guest — no account required.{' '}
            <Link
              to={`/login?next=${encodeURIComponent('/booking')}`}
              className="text-salon-gold hover:underline"
              onClick={persistDraftBeforeSignIn}
            >
              Sign in
            </Link>{' '}
            anytime to track requests in your dashboard.
          </p>
        )}

        {metaError && (
          <div
            role="alert"
            className="mb-6 rounded-2xl border border-salon-ink/10 bg-white border-l-4 border-l-amber-800/45 shadow-[0_12px_40px_-18px_rgba(0,0,0,0.12)] px-5 py-4 sm:px-6 sm:py-5"
          >
            <p className="text-[10px] uppercase tracking-[0.2em] text-amber-900/60 mb-2">Could not load</p>
            <p className="text-sm text-salon-ink leading-relaxed">{metaError}</p>
          </div>
        )}
        {msg && step !== 'success' && (
          <div
            role="alert"
            className={`mb-6 rounded-2xl border border-salon-ink/10 bg-white shadow-[0_12px_40px_-18px_rgba(0,0,0,0.12)] border-l-4 overflow-hidden ${
              msg.ok ? 'border-l-emerald-700/60' : 'border-l-red-800/50'
            }`}
          >
            <div className="px-5 py-4 sm:px-6 sm:py-5">
              <p
                className={`text-[10px] uppercase tracking-[0.2em] mb-2 ${
                  msg.ok ? 'text-emerald-800/70' : 'text-red-900/55'
                }`}
              >
                {msg.ok ? 'Request sent' : msg.code === 'email_registered' ? 'Sign in to book' : 'Could not book'}
              </p>
              <p className={`text-sm leading-relaxed ${msg.ok ? 'text-emerald-950/85' : 'text-salon-ink'}`}>
                {msg.text}
              </p>
              {!msg.ok && msg.code === 'email_registered' && (
                <Link
                  to={`/login?next=${encodeURIComponent('/booking')}`}
                  className="mt-5 inline-flex items-center justify-center rounded-lg bg-salon-ink px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-salon-ink/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-salon-gold focus-visible:ring-offset-2"
                  onClick={persistDraftBeforeSignIn}
                >
                  Sign in to continue
                </Link>
              )}
            </div>
          </div>
        )}

        {step === 'choose' && (
          <div className="bg-white border border-salon-ink/5 shadow-sm p-8 space-y-8">
            <div>
              <h2 className="text-sm uppercase tracking-widest text-salon-gold mb-2">Services</h2>
              <p className="text-xs text-salon-ink/50 mb-4">One service per appointment (one time slot).</p>
              <div className="space-y-6">
                {categories.map((cat) => (
                  <div
                    key={cat.id}
                    id={`booking-cat-${cat.id}`}
                    className={
                      preselect === cat.id
                        ? 'scroll-mt-28 rounded-lg border border-salon-gold/50 bg-salon-gold/10 p-4'
                        : undefined
                    }
                  >
                    <p className="font-medium text-salon-ink mb-2">{cat.name}</p>
                    <div className="flex flex-wrap gap-2">
                      {cat.services.map((s) => {
                        const key = `${cat.id}|${s.name}`;
                        const on = selectedService === key;
                        return (
                          <button
                            key={s.name}
                            type="button"
                            onClick={() => pickService(cat.id, s.name)}
                            className={`text-sm px-3 py-2 border rounded transition-colors ${
                              on ? 'bg-salon-gold/20 border-salon-gold' : 'border-salon-ink/10 hover:border-salon-gold/50'
                            }`}
                          >
                            {s.name} <span className="text-salon-gold text-xs">{s.price}</span>
                          </button>
                        );
                      })}
                    </div>
                  </div>
                ))}
              </div>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-8">
              <div>
                <label className="block text-[10px] uppercase tracking-widest text-salon-ink/70 mb-2">Date</label>
                <BookingDateCalendar availableDates={availableDates} value={date} onChange={setDate} />
              </div>
              <div>
                <label className="block text-[10px] uppercase tracking-widest text-salon-ink/70 mb-2">Time</label>
                {date ? (
                  <div className="mt-2 space-y-2">
                    <div className="flex flex-wrap gap-2">
                      {timesForDate.map((cell) => {
                        const sel = time === cell.time && cell.state === 'available';
                        const disabled = cell.state !== 'available';
                        return (
                          <button
                            key={cell.time}
                            type="button"
                            disabled={disabled}
                            onClick={() => cell.state === 'available' && setTime(cell.time)}
                            className={`min-w-[5.5rem] text-left text-sm px-3 py-2 border rounded transition-colors ${
                              sel
                                ? 'bg-salon-gold/25 border-salon-gold'
                                : disabled
                                  ? 'border-salon-ink/10 bg-salon-ink/[0.03] text-salon-ink/40 cursor-not-allowed'
                                  : 'border-salon-ink/15 hover:border-salon-gold/50 text-salon-ink'
                            }`}
                          >
                            <span className="font-medium">{formatTimeHm(cell.time)}</span>
                            {cell.state === 'booked' && (
                              <span className="block text-[10px] uppercase tracking-wider text-amber-800/80 mt-0.5">
                                Booked
                              </span>
                            )}
                            {cell.state === 'past' && (
                              <span className="block text-[10px] uppercase tracking-wider text-salon-ink/35 mt-0.5">
                                Past
                              </span>
                            )}
                          </button>
                        );
                      })}
                    </div>
                    <p className="text-xs text-salon-ink/45">Booked slots cannot be selected.</p>
                  </div>
                ) : (
                  <p className="text-sm text-salon-ink/50 mt-2">Choose a date to see times.</p>
                )}
              </div>
            </div>

            <button type="button" onClick={goReview} className="gold-button w-full py-3 flex items-center justify-center gap-2">
              Review request <ArrowRight className="w-4 h-4" />
            </button>
          </div>
        )}

        {step === 'review' && (
          <form onSubmit={onSubmit} className="space-y-8">
            {reviewBlock}

            {!isClientUser && (
              <div className="bg-white border border-salon-ink/5 shadow-sm p-8 space-y-4">
                <h3 className="text-sm font-medium text-salon-ink">Contact details</h3>
                <p className="text-xs text-salon-ink/55">We’ll use this to confirm your appointment.</p>
                <div>
                  <label className="block text-[10px] uppercase tracking-widest text-salon-ink/70 mb-1">Full name</label>
                  <input
                    value={guestName}
                    onChange={(e) => setGuestName(e.target.value)}
                    className="w-full border-b border-salon-ink/20 py-2 focus:border-salon-gold outline-none bg-transparent"
                    autoComplete="name"
                    required
                  />
                </div>
                <div>
                  <label className="block text-[10px] uppercase tracking-widest text-salon-ink/70 mb-1">Email</label>
                  <input
                    type="email"
                    value={guestEmail}
                    onChange={(e) => setGuestEmail(e.target.value)}
                    className="w-full border-b border-salon-ink/20 py-2 focus:border-salon-gold outline-none bg-transparent"
                    autoComplete="email"
                    required
                  />
                </div>
                <div>
                  <label className="block text-[10px] uppercase tracking-widest text-salon-ink/70 mb-1">Phone (optional)</label>
                  <input
                    type="tel"
                    value={guestPhone}
                    onChange={(e) => setGuestPhone(e.target.value)}
                    className="w-full border-b border-salon-ink/20 py-2 focus:border-salon-gold outline-none bg-transparent"
                    autoComplete="tel"
                  />
                </div>
              </div>
            )}

            <div className="flex flex-col-reverse sm:flex-row gap-3 sm:justify-between sm:items-center">
              <button
                type="button"
                onClick={() => {
                  setStep('choose');
                  setMsg(null);
                }}
                className="flex items-center justify-center gap-2 text-sm uppercase tracking-widest text-salon-ink/60 hover:text-salon-ink py-3"
              >
                <ArrowLeft className="w-4 h-4" /> Back
              </button>
              <button type="submit" disabled={submitting} className="gold-button py-3 px-8 flex items-center justify-center gap-2 disabled:opacity-50">
                {submitting ? 'Submitting…' : (
                  <>
                    Confirm booking <ArrowRight className="w-4 h-4" />
                  </>
                )}
              </button>
            </div>
          </form>
        )}

        {step === 'success' && (
          <div className="space-y-8">
            <div className="text-emerald-900 bg-emerald-50 border border-emerald-200/80 rounded-xl px-4 py-4">
              <p className="font-medium">You are booked</p>
              <p className="text-sm text-emerald-900/80 mt-1">
                {msg?.text || 'Your appointment is confirmed. Check your email for details.'}
              </p>
            </div>
            {reviewBlock}
            <div className="flex flex-col sm:flex-row gap-3 justify-center">
              <Link to="/" className="text-center text-sm border border-salon-ink/15 py-3 px-6 hover:border-salon-gold transition-colors">
                Back to home
              </Link>
              {isClientUser && (
                <Link to="/dashboard" className="text-center gold-button py-3 px-6">
                  Open dashboard
                </Link>
              )}
              {!isClientUser && (
                <Link to="/signup" className="text-center gold-button py-3 px-6">
                  Create an account
                </Link>
              )}
            </div>
          </div>
        )}

        {step === 'choose' && (
          <p className="mt-8 text-center">
            <button type="button" onClick={() => navigate(-1)} className="text-sm text-salon-ink/50 hover:text-salon-gold">
              ← Back
            </button>
          </p>
        )}
      </div>
    </div>
  );
}
