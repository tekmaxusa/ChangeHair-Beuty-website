import React, { useEffect, useState } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { Menu, X } from 'lucide-react';
import { AnimatePresence, motion } from 'motion/react';
import { apiLogout, useAuth } from '../context/AuthContext';

const NEXT_DASH = encodeURIComponent('/dashboard/');

/**
 * Primary nav aligned with PHP `site-header.php`: CLIENT DASHBOARD, MENU, LOG IN / SIGN UP or LOG OUT, BOOK APPOINTMENT.
 * On the home hero, the bar is transparent until scroll (matches public site).
 */
export default function SalonHeader() {
  const { user, loading, refreshMe, setUser } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();
  const [drawerOpen, setDrawerOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);

  const onHome = location.pathname === '/';

  useEffect(() => {
    if (!onHome) {
      setScrolled(true);
      return;
    }
    const onScroll = () => setScrolled(window.scrollY > 48);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
    return () => window.removeEventListener('scroll', onScroll);
  }, [onHome]);

  /** /menu and other routes are not "home", so nav stays high-contrast. */
  const heroMode = onHome && !scrolled && !drawerOpen;
  const ink = heroMode ? 'text-white' : 'text-salon-ink';
  const barClass = heroMode
    ? 'bg-transparent border-transparent shadow-none'
    : 'bg-[#f9f9f9] border-salon-ink/5 shadow-sm';

  const loginHref = `/login?next=${NEXT_DASH}`;

  const goBook = () => {
    setDrawerOpen(false);
    navigate('/booking');
  };

  const goMenu = () => {
    setDrawerOpen(false);
    navigate('/menu');
  };

  const onLogout = async () => {
    setDrawerOpen(false);
    setUser(null);
    await apiLogout();
    try {
      await refreshMe();
    } catch {
      setUser(null);
    }
    navigate('/', { replace: true });
  };

  const linkClass = `text-sm uppercase tracking-[0.2em] transition-colors cursor-pointer ${heroMode ? 'hover:text-salon-gold text-white' : 'hover:text-salon-gold text-salon-ink'}`;
  const quietClass = `text-sm uppercase tracking-[0.2em] transition-colors ${heroMode ? 'hover:text-salon-gold text-white/90' : 'hover:text-salon-gold text-salon-ink/80'}`;

  const bookBtnClass = heroMode
    ? 'text-xs uppercase tracking-[0.2em] border border-white text-white px-5 py-2 hover:bg-white hover:text-salon-ink transition-colors'
    : 'text-xs uppercase tracking-[0.2em] border border-salon-ink px-5 py-2 hover:bg-salon-ink hover:text-white transition-colors';

  const logoClass = heroMode
    ? 'text-base md:text-lg font-serif tracking-[0.15em] uppercase flex flex-col leading-tight bg-white/95 px-4 py-2'
    : `text-base md:text-lg font-serif tracking-[0.15em] uppercase flex flex-col leading-tight ${ink}`;


  return (
    <header className={`fixed top-0 left-0 right-0 z-[100] border-b transition-all duration-500 ${barClass}`} aria-label="Primary">
      <div className="max-w-7xl mx-auto px-6 md:px-8 flex justify-between items-center py-4 md:py-6">
        <Link
          to="/"
          className={logoClass}
          aria-label="Change Hair & Beauty home"
        >
          <span className={heroMode ? 'text-salon-ink' : undefined}>CHANGE HAIR</span>
          <span
            className={`text-[8px] md:text-[9px] tracking-[0.3em] mt-1 ${heroMode ? 'text-salon-ink/55' : 'opacity-50'}`}
          >
            &amp; BEAUTY
          </span>
        </Link>

        <nav className="hidden md:flex items-center gap-8 lg:gap-10">
          {user?.role === 'client' && (
            <Link to="/dashboard" className={linkClass}>
              Client dashboard
            </Link>
          )}
          <button type="button" onClick={goMenu} className={linkClass}>
            Menu
          </button>
          {!loading && !user && (
            <>
              <Link to={loginHref} className={quietClass}>
                Log in
              </Link>
              <Link to="/signup" className={quietClass}>
                Sign up
              </Link>
            </>
          )}
          {!loading && user && (
            <button type="button" onClick={() => void onLogout()} className={quietClass}>
              Log out
            </button>
          )}
          <button type="button" onClick={goBook} className={bookBtnClass}>
            Book appointment
          </button>
        </nav>

        <button
          type="button"
          className={`md:hidden p-2 ${ink}`}
          aria-label="Open menu"
          onClick={() => setDrawerOpen(true)}
        >
          <Menu className="w-6 h-6" />
        </button>
      </div>

      <AnimatePresence>
        {drawerOpen && (
          <motion.div
            initial={{ opacity: 0, x: '100%' }}
            animate={{ opacity: 1, x: 0 }}
            exit={{ opacity: 0, x: '100%' }}
            transition={{ type: 'tween', duration: 0.35 }}
            className="fixed inset-0 bg-white z-[110] flex flex-col items-center justify-center gap-8 md:hidden text-salon-ink"
          >
            <button type="button" className="absolute top-6 right-6 p-2" aria-label="Close menu" onClick={() => setDrawerOpen(false)}>
              <X className="w-6 h-6" />
            </button>
            {user?.role === 'client' && (
              <Link to="/dashboard" onClick={() => setDrawerOpen(false)} className="text-xl uppercase tracking-[0.25em] font-serif">
                Client dashboard
              </Link>
            )}
            <button type="button" onClick={goMenu} className="text-xl uppercase tracking-[0.25em] font-serif">
              Menu
            </button>
            {!loading && !user && (
              <>
                <Link to={loginHref} onClick={() => setDrawerOpen(false)} className="text-xl uppercase tracking-[0.25em] font-serif text-salon-gold">
                  Log in
                </Link>
                <Link to="/signup" onClick={() => setDrawerOpen(false)} className="text-xl uppercase tracking-[0.25em] font-serif">
                  Sign up
                </Link>
              </>
            )}
            {!loading && user && (
              <button type="button" onClick={() => void onLogout()} className="text-xl uppercase tracking-[0.25em] font-serif">
                Log out
              </button>
            )}
            <button type="button" onClick={goBook} className="gold-button mt-4">
              Book appointment
            </button>
          </motion.div>
        )}
      </AnimatePresence>
    </header>
  );
}
