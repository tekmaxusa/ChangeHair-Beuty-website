import React, { useMemo, useState } from 'react';
import { Link, Navigate, useNavigate, useSearchParams } from 'react-router-dom';
import { apiLogin, useAuth } from '../context/AuthContext';
import { nextToReactRoute, validatedInternalNext } from '../lib/nextRoute';

function apiOriginForOAuth(): string {
  const fromEnv = (import.meta.env.VITE_API_URL as string | undefined)?.replace(/\/$/, '') || '';
  if (fromEnv) {
    return fromEnv;
  }
  if (import.meta.env.DEV) {
    return 'http://localhost:8080';
  }
  return '';
}

function googleErrorMessage(code: string): string {
  if (code === 'not_configured') {
    return 'Google sign-in is not configured yet (add GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET).';
  }
  if (code === 'invalid_state') {
    return 'Google sign-in session expired. Please try again.';
  }
  if (code === 'auth_failed') {
    return 'Google sign-in failed. Please try again or use email and password.';
  }
  if (code === 'access_denied') {
    return 'Google sign-in was cancelled.';
  }
  if (code !== '') {
    return 'Google sign-in error. Please try again.';
  }
  return '';
}

export default function LoginPage() {
  const navigate = useNavigate();
  const [searchParams, setSearchParams] = useSearchParams();
  const { user, loading, refreshMe, setUser } = useAuth();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [pending, setPending] = useState(false);

  const nextInternal = useMemo(() => validatedInternalNext(searchParams.get('next')), [searchParams]);

  const googleErrParam = searchParams.get('google_err');
  const googleErrMsg = googleErrParam ? googleErrorMessage(googleErrParam) : '';

  const apiOrigin = apiOriginForOAuth();
  const googleStart =
    apiOrigin !== ''
      ? `${apiOrigin}/google-oauth-start.php?next=${encodeURIComponent(nextInternal)}`
      : '';

  const signupWithNextHref = `/signup?next=${encodeURIComponent(nextInternal)}`;

  if (!loading && user) {
    return <Navigate to={nextToReactRoute(nextInternal)} replace />;
  }

  const onSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setPending(true);
    try {
      const u = await apiLogin(email, password);
      setUser(u);
      await refreshMe();
      if (googleErrParam) {
        setSearchParams((prev) => {
          const p = new URLSearchParams(prev);
          p.delete('google_err');
          return p;
        });
      }
      navigate(nextToReactRoute(nextInternal));
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Login failed');
    } finally {
      setPending(false);
    }
  };

  const showGoogleFallback = apiOrigin === '';

  return (
    <div className="min-h-screen bg-salon-beige pt-28 pb-20 px-6">
      <div className="max-w-md mx-auto bg-white p-10 shadow-lg border border-salon-ink/5">
        <h1 className="text-2xl font-serif text-salon-ink mb-2">Sign in to your account</h1>
        <p className="text-sm text-salon-ink/60 mb-8">Client dashboard, booking requests, and account access.</p>
        {(error || googleErrMsg) && (
          <p className="text-sm text-red-600 mb-4">{error || googleErrMsg}</p>
        )}
        <form onSubmit={onSubmit} className="space-y-6">
          <div>
            <label className="block text-[10px] uppercase tracking-widest text-salon-ink/70 mb-2">
              Your email address
            </label>
            <input
              type="email"
              name="email"
              required
              autoComplete="username"
              placeholder="you@example.com"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="w-full border-b border-salon-ink/20 py-2 focus:border-salon-gold outline-none bg-transparent"
            />
          </div>
          <div>
            <div className="flex justify-between items-baseline gap-2 mb-2">
              <label className="block text-[10px] uppercase tracking-widest text-salon-ink/70">Your password</label>
              <Link to="/forgot-password" className="text-[10px] uppercase tracking-widest text-salon-gold hover:underline">
                Forgot?
              </Link>
            </div>
            <input
              type="password"
              name="password"
              required
              autoComplete="current-password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="w-full border-b border-salon-ink/20 py-2 focus:border-salon-gold outline-none bg-transparent"
            />
          </div>
          <button type="submit" disabled={pending} className="w-full gold-button py-3 disabled:opacity-50">
            {pending ? 'Signing in…' : 'Continue'}
          </button>
        </form>
        <div className="relative my-8">
          <span className="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white px-2 text-[10px] uppercase text-salon-gold">
            Or
          </span>
          <hr className="border-salon-ink/10" />
        </div>
        {showGoogleFallback ? (
          <a
            href="https://accounts.google.com/"
            target="_blank"
            rel="noopener noreferrer"
            className="block w-full text-center border border-salon-ink/20 py-3 text-sm uppercase tracking-widest hover:border-salon-gold transition-colors"
          >
            <span className="font-semibold text-salon-ink/80 mr-1">G</span> Continue with Google
          </a>
        ) : (
          <a
            href={googleStart}
            className="block w-full text-center border border-salon-ink/20 py-3 text-sm uppercase tracking-widest hover:border-salon-gold transition-colors"
          >
            <span className="font-semibold text-salon-ink/80 mr-1">G</span> Continue with Google
          </a>
        )}
        {showGoogleFallback && (
          <p className="text-xs text-salon-ink/50 mt-2">Set VITE_API_URL for Google sign-in through this site.</p>
        )}
        <p className="text-sm text-salon-ink/60 mt-8">
          No account?{' '}
          <Link to={signupWithNextHref} className="text-salon-gold hover:underline">
            Sign up
          </Link>
        </p>
        <p className="text-sm mt-4">
          <Link to="/" className="text-salon-ink/50 hover:text-salon-gold">
            ← Back to site
          </Link>
        </p>
      </div>
    </div>
  );
}
