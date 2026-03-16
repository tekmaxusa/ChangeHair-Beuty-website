import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import { 
  Instagram, 
  Facebook, 
  MapPin, 
  Phone, 
  Mail, 
  Clock, 
  ChevronLeft, 
  ChevronRight, 
  X,
  Menu,
  ArrowRight,
  MessageSquare,
  Sparkles,
  ShieldCheck,
  Heart,
  Scissors
} from 'lucide-react';
import gallery1 from './assets/gallery/gallery-1.png';
import gallery2 from './assets/gallery/gallery-2.png';
import gallery3 from './assets/gallery/gallery-3.png';
import gallery4 from './assets/gallery/gallery-4.png';
import gallery5 from './assets/gallery/gallery-5.png';
import gallery6 from './assets/gallery/gallery-6.png';
import gallery7 from './assets/gallery/gallery-7.png';
import gallery8 from './assets/gallery/gallery-8.png';
import gallery9 from './assets/gallery/gallery-9.png';
import smpBeforeAfter from './assets/smp-before-after.png';
import signatureColor from './assets/signature-color.png';
import signaturePerm from './assets/signature-perm.png';

// --- Types ---
interface ServiceItem {
  name: string;
  price: string;
  note?: string;
}

interface ServiceCategory {
  category: string;
  items: ServiceItem[];
}

interface Testimonial {
  id: number;
  name: string;
  text: string;
}

// --- Data --- (Price list: Change Hair & Beauty)
const SERVICE_MENU: ServiceCategory[] = [
  {
    category: 'CUT',
    items: [
      { name: 'Women', price: '$35+' },
      { name: 'Men', price: '$25+' },
      { name: 'Kids', price: '$25+' },
    ]
  },
  {
    category: 'COLOR',
    items: [
      { name: 'Root', price: '$80+' },
      { name: 'Manicure', price: '$80+' },
      { name: 'Highlight (F)', price: '$200+' },
      { name: 'Highlight (M)', price: '$150+' },
    ]
  },
  {
    category: 'PERM',
    items: [
      { name: "Men's Iron Perm", price: '$130+' },
      { name: "Basic Women's Perm", price: '$100+' },
      { name: 'Set / Digital', price: '$200+' },
      { name: 'Magic Setting', price: '$250+' },
      { name: 'Japanese Magic Straight', price: '$230+' },
    ]
  },
  {
    category: 'STYLE',
    items: [
      { name: 'Shampoo', price: '$20+' },
      { name: 'Blow Dry', price: '$35+' },
      { name: 'Upstyle', price: '$130+' },
      { name: 'Makeup', price: '$150+' },
    ]
  }
];

const TESTIMONIALS: Testimonial[] = [
  {
    id: 1,
    name: "Jimmy Nguyen",
    text: "This place is so amazing. I met with Miss Young, I think she's the owner there. I asked for a hair cut and for a short men's curly hair perm and she did absolutely amazing! The timing was way quicker than I expected but she was so friendly and suggested I try a down curl pattern and it definitely fits my appearance well! I asked for a fade and she did a great job on my fade too! I will definitely be coming back!"
  },
  {
    id: 2,
    name: "Cathy Ngo",
    text: "Change Hair & Beauty has a new owner, her name is Soyoung (Young) and I've been following her for the past few years ever since she was located at Pika Pika in Plano. She has always taken good care of my hair for a very fair price and it was all done within 30mins! I truly underestimated her skills. If she saw that my hair needed some extra maintenance, she would be honest about it and provide solutions. She even gifted a hair essence for my dry ends. They also have punch cards where your 3rd haircut will get 20% off! I can't wait to see how far her business will grow."
  },
  {
    id: 3,
    name: "Aaborr Len",
    text: "I had a great experience here! I got my hair trimmed, coloured, and a keratin treatment and I'm so happy with the results. Young was lovely and very professional. Booking was easy, and when I had an inconvenience, she did her best to adjust the appointment and fit me in, which I really appreciated. Her pricing is also very reasonable for the quality she provides. Highly recommend!"
  },
  {
    id: 4,
    name: "Y P",
    text: "I stopped by Zion Market recently and was looking for a hair salon to get my husband's hair cut when we happened to walk past this place and decided to go in. I'm so glad we did. The hair stylist cut my husband's hair quickly and beautifully. Because we were so happy with the result, I brought my daughter back for a haircut as well. She absolutely loves it. They are incredibly kind and professional, and their prices are amazing — $25 for men's cuts and $35 for women's cuts. I highly recommend this place!"
  },
  {
    id: 5,
    name: "Justin Nguyen",
    text: "Really amazing haircut! Been going to Kim for many years and she opened a new place! Every time I go, the quality is always consistent and you can see the confidence in her work. She really makes sure that the haircut is up to standard. On the plus side, it's great pricing for that level of quality! Definitely recommend!!"
  },
  {
    id: 6,
    name: "Monica Nguyen",
    text: "This is the hair salon to go to if you're in the area! Ask for Young and she will carefully take care of your haircut. She has been my hair stylist for 5+ years now, because I love the fact she takes the time to ask what I want and looks at all the references I have. I have never had a bad haircut with her when it comes to my long, thick hair. I will always come back to have her cut and wash my hair. The salon is very clean and spacious as well, so you can bring your family there too."
  },
];

const INSTAGRAM_URL = 'https://www.instagram.com/changehairbeauty/';
const FACEBOOK_URL = 'https://www.facebook.com/changehairbeauty/';
const TAWK_CHAT_URL = 'https://tawk.to/chat/69b675beb2bda41c36e81e18/';
const GALLERY_POST_URLS = [
  'https://www.instagram.com/changehairbeauty/p/CH3c-r8peN8/',
  'https://www.instagram.com/changehairbeauty/p/CH3cqVMpawA/',
  'https://www.instagram.com/changehairbeauty/p/CH3cWLZp9Gp/',
  'https://www.instagram.com/changehairbeauty/p/CGQgoGvJfgB/',
  'https://www.instagram.com/changehairbeauty/p/CFxehDtpY6A/',
  'https://www.instagram.com/changehairbeauty/p/CFvDxL3pa-e/',
  'https://www.instagram.com/changehairbeauty/p/CFsStwdpLss/',
  'https://www.instagram.com/changehairbeauty/p/CDFsOsfhIK0/',
  'https://www.instagram.com/changehairbeauty/p/CDFq_-nBtrZ/',
];
const GALLERY_ITEMS: { url: string; img: string }[] = [
  { url: GALLERY_POST_URLS[0], img: gallery1 },
  { url: GALLERY_POST_URLS[1], img: gallery2 },
  { url: GALLERY_POST_URLS[2], img: gallery3 },
  { url: GALLERY_POST_URLS[3], img: gallery4 },
  { url: GALLERY_POST_URLS[4], img: gallery5 },
  { url: GALLERY_POST_URLS[5], img: gallery6 },
  { url: GALLERY_POST_URLS[6], img: gallery7 },
  { url: GALLERY_POST_URLS[7], img: gallery8 },
  { url: GALLERY_POST_URLS[8], img: gallery9 },
];

// Booking modal: categories match "Our Services" / price list
const BOOKING_CATEGORIES = [
  { id: 'cut', name: 'Cut', services: [{ name: 'Women', price: '$35+' }, { name: 'Men', price: '$25+' }, { name: 'Kids', price: '$25+' }] },
  { id: 'color', name: 'Color', services: [{ name: 'Root', price: '$80+' }, { name: 'Manicure', price: '$80+' }, { name: 'Highlight (F)', price: '$200+' }, { name: 'Highlight (M)', price: '$150+' }] },
  { id: 'perm', name: 'Perm', services: [{ name: "Men's Iron Perm", price: '$130+' }, { name: "Basic Women's Perm", price: '$100+' }, { name: 'Set / Digital', price: '$200+' }, { name: 'Magic Setting', price: '$250+' }, { name: 'Japanese Magic Straight', price: '$230+' }] },
  { id: 'style', name: 'Style', services: [{ name: 'Shampoo', price: '$20+' }, { name: 'Blow Dry', price: '$35+' }, { name: 'Upstyle', price: '$130+' }, { name: 'Makeup', price: '$150+' }] },
];

const TIME_SLOTS = ['10:00 am', '10:30 am', '11:00 am', '11:30 am', '12:00 pm', '12:30 pm', '1:00 pm', '1:30 pm', '2:00 pm', '2:30 pm', '3:00 pm', '3:30 pm', '4:00 pm', '4:30 pm', '5:00 pm', '5:30 pm', '6:00 pm'];
const AGENT_NAME = 'Change Hair & Beauty';
const LOCATION_NAME = 'The Vista';
const LOCATION_ADDRESS = '2405 S Stemmons Fwy Ste 1126, Lewisville, TX 75067';
const LOCATION_PHONE = '+1 214-488-1122';
const LOCATION_HOURS = [
  'Saturday: 10 AM – 7 PM',
  'Sunday: 1 – 6 PM',
  'Monday: Closed',
  'Tuesday: 10 AM – 7 PM',
  'Wednesday: 10 AM – 7 PM',
  'Thursday: 10 AM – 7 PM',
  'Friday: 10 AM – 7 PM',
];
const MAP_EMBED_URL = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3350.789!2d-97.006!3d33.046!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x864c3e5d8e8e8e8e%3A0x0!2s2405%20S%20Stemmons%20Fwy%20%231126%2C%20Lewisville%2C%20TX%2075067!5e0!3m2!1sen!2sus';
const MAP_SEARCH_URL = 'https://www.google.com/maps/search/2405+S+Stemmons+Fwy+Ste+1126+Lewisville+TX+75067';

const BLOG_POSTS = [
  { id: 'diy-shampoo-bleaching', title: 'DIY Shampoo Recipes to Restore Your Hair After Bleaching and Dyeing', date: 'November 1, 2024', author: 'devzons', summary: 'Bleaching and dyeing your hair can dramatically transform your look, but these processes often leave your locks feeling dry, brittle, and damaged. To help restore your hair\'s health and vitality, it\'s essential to provide it with the right care and nourishment. In this post, we\'ll share easy-to-make DIY shampoo recipes using natural ingredients that aid in recovering and strengthening your hair after bleaching and dyeing.' },
  { id: 'head-spa', title: 'Why Do We Need a Head Spa? The Benefits of Scalp Care for Overall Wellness', date: 'October 5, 2024', author: 'devzons', summary: 'While we often focus on skincare and haircare, the scalp is the foundation of healthy hair. Head spa treatments address scalp health with deep cleansing, exfoliation, massage, and nourishment—promoting hair growth, relieving stress, and improving overall wellness.' },
  { id: 'synthetic-preservatives', title: 'Are Synthetic Preservatives Really Worth the Risk?', date: 'October 5, 2024', author: 'devzons', summary: 'Preservatives play a crucial role in personal care and beauty products, ensuring safety and effectiveness. Phenoxyethanol and Sodium Benzoate are common synthetic preservatives, but concerns about their use continue to drive debate and interest in natural alternatives.' },
  { id: 'diy-indian-shampoo', title: 'DIY Indian Shampoo Recipes for Hair Loss', date: 'November 1, 2024', author: 'devzons', summary: 'Hair loss can stem from genetics, stress, or nutritional deficiencies. Traditional Indian natural ingredients—such as Amla, Shikakai, and Neem—have been used for generations in shampoos to strengthen follicles, promote growth, and restore hair health.' },
];

// --- Components ---

const Navbar = ({ onDashboardClick, showDashboard, onMenuClick, showMenu, onBack, onOpenBooking }: { onDashboardClick: () => void; showDashboard: boolean; onMenuClick: () => void; showMenu: boolean; onBack: () => void; onOpenBooking: () => void }) => {
  const [isScrolled, setIsScrolled] = useState(false);
  const [isMenuOpen, setIsMenuOpen] = useState(false);

  useEffect(() => {
    const handleScroll = () => setIsScrolled(window.scrollY > 50);
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const navLinks = (
    <>
      <button type="button" onClick={() => { onDashboardClick(); setIsMenuOpen(false); }} className={`text-xs uppercase tracking-[0.2em] transition-colors cursor-pointer ${showDashboard ? 'text-salon-gold' : 'hover:text-salon-gold'}`}>
        Client Dashboard
      </button>
      <button type="button" onClick={() => { onMenuClick(); setIsMenuOpen(false); }} className={`text-xs uppercase tracking-[0.2em] transition-colors cursor-pointer ${showMenu ? 'text-salon-gold' : 'hover:text-salon-gold'}`}>
        Menu
      </button>
      <button type="button" className="text-xs uppercase tracking-[0.2em] border border-salon-ink px-6 py-2 hover:bg-salon-ink hover:text-white transition-all cursor-pointer" onClick={() => { onOpenBooking(); setIsMenuOpen(false); }}>
        Book Appointment
      </button>
    </>
  );

  return (
    <nav className={`fixed w-full z-50 transition-all duration-700 ${isScrolled || showDashboard || showMenu ? 'bg-white/95 backdrop-blur-sm py-4 shadow-sm' : 'bg-transparent py-8'}`}>
      <div className="max-w-7xl mx-auto px-8 flex justify-between items-center">
        <button type="button" onClick={onBack} className="text-xl font-serif tracking-[0.2em] uppercase flex flex-col items-center text-left cursor-pointer">
          <span className="text-salon-ink">CHANGE HAIR</span>
          <span className="text-[8px] tracking-[0.4em] -mt-1 opacity-50">{' & BEAUTY'}</span>
        </button>
        
        <div className="hidden md:flex space-x-12 items-center">
          {navLinks}
        </div>

        <button className="md:hidden" onClick={() => setIsMenuOpen(!isMenuOpen)}>
          <Menu className="w-5 h-5" />
        </button>
      </div>

      <AnimatePresence>
        {isMenuOpen && (
          <motion.div 
            initial={{ opacity: 0, x: '100%' }}
            animate={{ opacity: 1, x: 0 }}
            exit={{ opacity: 0, x: '100%' }}
            transition={{ type: 'tween', duration: 0.4 }}
            className="fixed inset-0 bg-white z-[60] flex flex-col items-center justify-center space-y-8"
          >
            <button className="absolute top-8 right-8" onClick={() => setIsMenuOpen(false)}>
              <X className="w-6 h-6" />
            </button>
            <button type="button" onClick={() => { onDashboardClick(); setIsMenuOpen(false); }} className="text-lg uppercase tracking-[0.3em] font-serif cursor-pointer">
              Client Dashboard
            </button>
            <button type="button" onClick={() => { onMenuClick(); setIsMenuOpen(false); }} className="text-lg uppercase tracking-[0.3em] font-serif cursor-pointer">
              Menu
            </button>
            <button type="button" className="gold-button !mt-12 cursor-pointer" onClick={() => { onOpenBooking(); setIsMenuOpen(false); }}>
              Book Appointment
            </button>
          </motion.div>
        )}
      </AnimatePresence>
    </nav>
  );
};

const ClientDashboard = ({ onOpenBooking }: { onOpenBooking?: () => void } = {}) => (
  <div className="min-h-screen bg-salon-beige pt-28 pb-20 px-6 md:px-12">
    <div className="max-w-6xl mx-auto">
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-start">
        {/* Sign-in card */}
        <div className="bg-white p-10 md:p-12 shadow-lg border border-salon-ink/5">
          <h2 className="text-2xl font-serif text-salon-ink mb-8">Sign in to your account</h2>
          <form className="space-y-6" onSubmit={(e) => e.preventDefault()}>
            <div>
              <label className="block text-[10px] uppercase tracking-widest text-salon-ink/70 mb-2">Your Email Address</label>
              <input type="email" className="w-full border-b border-salon-ink/20 py-2 focus:border-salon-gold outline-none transition-colors bg-transparent" placeholder="you@example.com" />
            </div>
            <div>
              <div className="flex justify-between items-center mb-2">
                <label className="block text-[10px] uppercase tracking-widest text-salon-ink/70">Your Password</label>
                <a href="#" className="text-[10px] text-salon-gold hover:underline">Forgot?</a>
              </div>
              <input type="password" className="w-full border-b border-salon-ink/20 py-2 focus:border-salon-gold outline-none transition-colors bg-transparent" />
            </div>
            <button type="submit" className="w-full gold-button py-4">Continue</button>
            <div className="relative my-8">
              <span className="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white px-4 text-[10px] uppercase tracking-widest text-salon-ink/50">Or</span>
              <hr className="border-salon-ink/10" />
            </div>
            <button type="button" onClick={() => window.open('https://accounts.google.com/', '_blank')} className="w-full border border-salon-ink/20 py-3 text-sm uppercase tracking-widest hover:border-salon-gold hover:text-salon-gold transition-colors flex items-center justify-center gap-2">
              <span className="font-semibold text-salon-ink/80">G</span> Continue with Google
            </button>
          </form>
        </div>

        {/* Salon info + hours */}
        <div className="space-y-10">
          <div>
            <h2 className="text-2xl font-serif text-salon-ink mb-1">Change Hair <span className="text-salon-gold">|</span> Beauty</h2>
            <p className="text-sm text-salon-ink/80 mt-4 leading-relaxed">{LOCATION_NAME}<br />{LOCATION_ADDRESS}</p>
            <div className="mt-6 space-y-2 text-sm text-salon-ink/80">
              <p><span className="text-[10px] uppercase tracking-widest text-salon-gold">Phone:</span> <a href="tel:+12144881122" className="hover:text-salon-gold">{LOCATION_PHONE}</a></p>
            </div>
          </div>
          <div>
            <p className="text-[10px] uppercase tracking-widest text-salon-gold mb-4">Operating Hours</p>
            <ul className="text-sm text-salon-ink/80 space-y-2">
              {LOCATION_HOURS.map((line, i) => <li key={i}>{line}</li>)}
            </ul>
          </div>
        </div>
      </div>

      {/* Map - grayscale like reference, hover for color */}
      <div className="mt-20">
        <p className="text-[10px] uppercase tracking-widest text-salon-gold mb-4">Location</p>
        <div className="relative w-full border border-salon-ink/10 overflow-hidden bg-salon-ink/5 grayscale hover:grayscale-0 opacity-95 hover:opacity-100 transition-all duration-500" style={{ height: 400 }}>
          <iframe
            title="Change Hair & Beauty location"
            src={MAP_EMBED_URL}
            width="100%"
            height="100%"
            style={{ border: 0, position: 'absolute', left: 0, top: 0, width: '100%', height: '100%', display: 'block' }}
            allowFullScreen
            referrerPolicy="no-referrer-when-downgrade"
          />
        </div>
        <a href={MAP_SEARCH_URL} target="_blank" rel="noopener noreferrer" className="inline-block mt-3 text-[10px] uppercase tracking-widest text-salon-gold hover:underline">View on Google Maps</a>
      </div>
    </div>
  </div>
);

const BlogView = () => {
  const [selectedId, setSelectedId] = useState<string | null>(null);
  const post = selectedId ? BLOG_POSTS.find(p => p.id === selectedId) : null;

  const BlogFooter = () => (
    <footer className="bg-white border-t border-salon-ink/10 py-16 px-8 mt-20">
      <div className="max-w-7xl mx-auto">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-12">
          <div>
            <h3 className="text-xl font-serif text-salon-ink mb-2">Change Hair <span className="text-salon-gold">|</span> Beauty</h3>
            <p className="text-sm text-salon-ink/80">{LOCATION_NAME} — {LOCATION_ADDRESS}</p>
            <div className="mt-4 space-y-1 text-sm text-salon-ink/80">
              <p>Phone: {LOCATION_PHONE}</p>
            </div>
          </div>
          <div>
            <p className="text-[10px] uppercase tracking-widest text-salon-gold mb-3">Operating Hours</p>
            <ul className="text-sm text-salon-ink/80 space-y-1">
              {LOCATION_HOURS.map((line, i) => <li key={i}>{line}</li>)}
            </ul>
          </div>
        </div>
        <div className="w-full mb-8">
            <p className="text-[10px] uppercase tracking-widest text-salon-gold mb-3">Location</p>
            <div className="relative w-full rounded border border-salon-ink/10 overflow-hidden bg-salon-ink/5 grayscale hover:grayscale-0 opacity-95 hover:opacity-100 transition-all duration-500" style={{ height: 400 }}>
              <iframe
                title="Change Hair & Beauty location - Blog"
                src={MAP_EMBED_URL}
                width="100%"
                height="100%"
                style={{ border: 0, position: 'absolute', left: 0, top: 0, width: '100%', height: '100%', display: 'block' }}
                allowFullScreen
                referrerPolicy="no-referrer-when-downgrade"
              />
            </div>
            <a href={MAP_SEARCH_URL} target="_blank" rel="noopener noreferrer" className="inline-block mt-2 text-[10px] uppercase tracking-widest text-salon-gold hover:underline">View on Google Maps</a>
          </div>
        <p className="text-[10px] uppercase tracking-[0.2em] opacity-40 mt-8">Copyright © {new Date().getFullYear()}, Change Hair &amp; Beauty</p>
      </div>
    </footer>
  );

  if (post) {
    return (
      <div className="min-h-screen bg-salon-beige pt-24 pb-0">
        <div className="max-w-7xl mx-auto px-8 py-16">
          <button type="button" onClick={() => setSelectedId(null)} className="flex items-center gap-2 text-salon-gold hover:underline text-sm uppercase tracking-widest mb-8">
            <ChevronLeft className="w-4 h-4" /> Back to Blog
          </button>
          <article className="bg-white rounded-none shadow-sm border border-salon-ink/5 p-10 md:p-16">
            <h1 className="text-3xl md:text-5xl font-serif text-salon-ink mb-4">{post.title}</h1>
            <p className="text-[10px] uppercase tracking-widest text-salon-ink/50 mb-10">By {post.author} · {post.date}</p>
            <div className="max-w-none text-base md:text-lg leading-relaxed">
              <BlogPostContent id={post.id} />
            </div>
            <section className="mt-20 pt-10 border-t border-salon-ink/10">
              <h3 className="text-lg font-serif text-salon-ink mb-4">Leave a Reply</h3>
              <p className="text-sm text-salon-ink/60 mb-4">You must be logged in to post a comment.</p>
              <textarea placeholder="Comment *" rows={4} className="w-full border border-salon-ink/20 p-4 focus:border-salon-gold outline-none bg-transparent resize-none text-sm" />
              <button type="button" className="mt-4 bg-salon-ink text-white px-8 py-3 text-[10px] uppercase tracking-widest hover:bg-salon-gold transition-colors">Post Comment</button>
            </section>
          </article>
        </div>
        <BlogFooter />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-salon-beige pt-24 pb-0">
      <div className="max-w-7xl mx-auto px-8 py-16">
        <h1 className="text-4xl font-serif text-salon-ink mb-12">Blog</h1>
        <div className="space-y-14">
          {BLOG_POSTS.map((p) => (
            <div key={p.id} className="border-b border-salon-ink/10 pb-14 last:border-0">
              <p className="text-[10px] uppercase tracking-widest text-salon-ink/50 mb-2">By {p.author}</p>
              <p className="text-sm text-salon-ink/80 leading-relaxed mb-4">{p.summary}</p>
              <button type="button" onClick={() => setSelectedId(p.id)} className="text-blue-600 hover:underline font-medium mb-2 block text-left">
                Read More
              </button>
              <p className="text-[10px] text-salon-ink/50 mb-2">{p.date}</p>
              <button type="button" onClick={() => setSelectedId(p.id)} className="text-xl font-serif text-blue-600 hover:underline transition-colors block text-left">
                {p.title}
              </button>
            </div>
          ))}
        </div>
        <BlogFooter />
      </div>
    </div>
  );
};

const BlogPostContent = ({ id }: { id: string }) => {
  const content = (
    <div className="prose prose-salon max-w-none text-salon-ink/90 text-sm leading-relaxed space-y-6">
      {id === 'diy-shampoo-bleaching' && (
        <>
          <p>Bleaching and dyeing your hair can dramatically transform your look, but these processes often leave your locks feeling dry, brittle, and damaged. To help restore your hair&apos;s health and vitality, it&apos;s essential to provide it with the right care and nourishment. In this post, we&apos;ll share easy-to-make DIY shampoo recipes using natural ingredients that aid in recovering and strengthening your hair after bleaching and dyeing. These recipes are not only effective but also gentle on your hair and scalp.</p>
          <h2 className="text-xl font-serif text-salon-ink mt-10 mb-4">1. Avocado and Olive Oil Shampoo</h2>
          <p className="font-medium text-salon-ink/80">Ingredients:</p>
          <ul className="list-disc pl-6 space-y-1"><li>1 ripe avocado</li><li>2 tbsp olive oil</li><li>2 tbsp coconut oil</li><li>1 cup mild base shampoo</li></ul>
          <p className="font-medium text-salon-ink/80 mt-4">Instructions:</p>
          <ol className="list-decimal pl-6 space-y-2"><li>Mash avocado and mix with oils, then combine with base shampoo.</li><li>Store in a clean container. Use as regular shampoo; massage into scalp and hair, rinse with lukewarm water.</li></ol>
          <p><strong>Benefits:</strong> Avocado is rich in vitamin E and omega-3 fatty acids for deep nourishment. Olive and coconut oils soften hair, add shine, and reduce breakage.</p>
          <h2 className="text-xl font-serif text-salon-ink mt-10 mb-4">2. Aloe Vera and Honey Shampoo</h2>
          <p className="font-medium text-salon-ink/80">Ingredients:</p>
          <ul className="list-disc pl-6 space-y-1"><li>2 tbsp fresh aloe vera gel</li><li>2 tbsp honey</li><li>1 cup mild base shampoo</li><li>4 drops lavender essential oil (optional)</li></ul>
          <p><strong>Benefits:</strong> Aloe vera soothes the scalp and promotes growth; honey locks in moisture. Lavender supports scalp health.</p>
          <h2 className="text-xl font-serif text-salon-ink mt-10 mb-4">3. Chamomile Vegan Shampoo</h2>
          <ul className="list-disc pl-6 space-y-1"><li>2 chamomile tea bags, 1 cup vegan base shampoo</li><li>2 tbsp apple cider vinegar, 4 drops lavender oil (optional)</li></ul>
          <p><strong>Benefits:</strong> Chamomile adds shine and softness; apple cider vinegar balances pH and removes buildup.</p>
          <h2 className="text-xl font-serif text-salon-ink mt-10 mb-4">4. Egg and Honey Protein Shampoo</h2>
          <ul className="list-disc pl-6 space-y-1"><li>1 egg, 2 tbsp honey, 1 tbsp olive oil, 1 tbsp baking soda, 1 cup water</li></ul>
          <p><strong>Benefits:</strong> Eggs provide protein to strengthen and repair hair; honey and olive oil moisturize; baking soda cleanses the scalp.</p>
          <h2 className="text-xl font-serif text-salon-ink mt-10 mb-4">5. Yogurt and Honey Nourishing Shampoo</h2>
          <ul className="list-disc pl-6 space-y-1"><li>2 tbsp plain yogurt, 2 tbsp honey, 1 cup vegan base shampoo, 4 drops lemon essential oil (optional)</li></ul>
          <p><strong>Benefits:</strong> Yogurt is rich in protein and probiotics for healthy growth; honey moisturizes; lemon oil refreshes the scalp.</p>
          <p className="mt-10">Recovering from damage caused by bleaching and dyeing requires consistent care and the right nutrients. These DIY recipes use natural ingredients to moisturize, condition, and repair your hair. By incorporating them into your routine, you can help rejuvenate your locks—stronger, shinier, and healthier.</p>
        </>
      )}
      {id === 'head-spa' && (
        <>
          <p>While we often focus on skincare and haircare, the scalp is the foundation of healthy hair. Head spa treatments address scalp health with deep cleansing, exfoliation, massage, and nourishment.</p>
          <h2 className="text-xl font-serif text-salon-ink mt-10 mb-4">What Is a Head Spa?</h2>
          <p>A head spa is a specialized treatment focused on scalp health: deep cleansing, exfoliation, massage, and nourishment. It helps remove buildup, exfoliate dead skin, stimulate circulation for hair growth, and relieve stress through scalp massage and aromatherapy. Head spas originated in Japan and are popular worldwide.</p>
          <h2 className="text-xl font-serif text-salon-ink mt-10 mb-4">Why Is Scalp Care So Important?</h2>
          <p><strong>Healthy Scalp = Healthy Hair.</strong> The scalp is like soil for your hair—when it&apos;s unhealthy, follicles suffer and hair can thin or fall out. Poor circulation, oiliness, or dryness lead to flaky or irritated scalp.</p>
          <p><strong>Removes Buildup</strong> from styling products that can dull hair and clog follicles. <strong>Promotes Hair Growth</strong> by improving blood flow and delivering nutrients to follicles. <strong>Balances Oil Production</strong> for both oily and dry scalps.</p>
          <h2 className="text-xl font-serif text-salon-ink mt-10 mb-4">Mental and Physical Wellness Benefits</h2>
          <p><strong>Stress Relief:</strong> Head spas are deeply relaxing—they can lower stress, alleviate headaches, and improve mood and mental clarity. <strong>Better Sleep:</strong> Scalp massage and treatments can promote better sleep. <strong>Boosts Confidence:</strong> A healthy scalp and good hair support confidence.</p>
          <h2 className="text-xl font-serif text-salon-ink mt-10 mb-4">Conditions That Benefit from Head Spa</h2>
          <ul className="list-disc pl-6 space-y-1"><li>Dandruff: deep cleansing and exfoliation</li><li>Oily scalp: control oil production</li><li>Dry or itchy scalp: nourishing, moisturizing treatments</li><li>Hair thinning: improved blood flow to the scalp</li></ul>
          <h2 className="text-xl font-serif text-salon-ink mt-10 mb-4">How Often Should You Get a Head Spa?</h2>
          <p>It depends on your needs. For oily or problematic scalps: weekly or bi-weekly. For dry or normal scalps: once every month or two. For specific concerns, ask your stylist.</p>
          <p className="mt-10">A head spa goes beyond aesthetics—it supports overall hair and scalp health and mental well-being. Have you tried a head spa? Share your experience in the comments.</p>
        </>
      )}
      {id === 'synthetic-preservatives' && (
        <>
          <p>Preservatives play a crucial role in beauty products—they prevent bacteria, mold, and fungi. Popular synthetic preservatives like <strong>Phenoxyethanol</strong> and <strong>Sodium Benzoate</strong> are common, but concerns about their use persist.</p>
          <h2 className="text-xl font-serif text-salon-ink mt-10 mb-4">What Are Synthetic Preservatives, and Why Are They Used?</h2>
          <p>They extend shelf life, prevent contamination, and are cost-effective. <strong>Phenoxyethanol</strong> is an antimicrobial that maintains product integrity. <strong>Sodium Benzoate</strong> is effective against fungi and bacteria.</p>
          <h2 className="text-xl font-serif text-salon-ink mt-10 mb-4">Are Synthetic Preservatives Harmful?</h2>
          <p><strong>Phenoxyethanol</strong> is used at under 1% and regulated by the FDA and European Commission. Potential risks: skin irritation, possible nervous system effects at high doses, and questions about long-term exposure. <strong>Sodium Benzoate</strong> is often used with Vitamin C in acidic products; when combined with Vitamin C and exposed to heat/light, it can form <strong>Benzene</strong>, a known carcinogen.</p>
          <h2 className="text-xl font-serif text-salon-ink mt-10 mb-4">Is It Really Worth the Risk?</h2>
          <p>Arguments in favor: regulations exist; they prevent contamination and ensure product safety. Concerns: &quot;safe&quot; quantities are debated; sensitive skin or allergies may react; potential long-term risks remain.</p>
          <h2 className="text-xl font-serif text-salon-ink mt-10 mb-4">Natural Alternatives</h2>
          <p><strong>Leucidal Liquid (Radish Root Ferment)</strong>, <strong>Rosemary Extract</strong>, <strong>Vitamin E (Tocopherol)</strong>, and <strong>Grapefruit Seed Extract</strong> offer gentler options. Pros: gentler on skin, free from synthetic chemicals. Cons: shorter shelf life (1–2 months, may need refrigeration), limited efficacy compared to synthetics.</p>
          <h2 className="text-xl font-serif text-salon-ink mt-10 mb-4">Conclusion: Weighing the Pros and Cons</h2>
          <p>Whether synthetic preservatives are &quot;worth it&quot; depends on your preferences, health, and lifestyle. Prioritizing long shelf life and affordability may lead you to synthetics; prioritizing minimal chemicals or sensitive skin may lead you to natural options. Check labels, do your research, and choose what aligns with your health priorities. What&apos;s your take? Share your thoughts in the comments below.</p>
        </>
      )}
      {id === 'diy-indian-shampoo' && (
        <>
          <p>Hair loss can stem from genetics, stress, or nutritional deficiencies. Traditional Indian natural ingredients have been used for generations to strengthen follicles, prevent hair loss, and promote growth. Key ingredients and how to use them:</p>
          <h2 className="text-xl font-serif text-salon-ink mt-10 mb-4">Amla (Indian Gooseberry)</h2>
          <p><strong>Benefits:</strong> Rich in Vitamin C, strengthens hair follicles, prevents hair loss, promotes growth, antioxidant properties, enhances shine and flexibility. <strong>How to use:</strong> Create a paste with Amla powder and water; add to shampoo or use as an extract.</p>
          <h2 className="text-xl font-serif text-salon-ink mt-10 mb-4">Shikakai (Acacia Concinna)</h2>
          <p><strong>Benefits:</strong> Natural cleanser, gently cleanses scalp, removes impurities, maintains natural oils, enhances shine, prevents hair loss, stimulates scalp for growth. <strong>How to use:</strong> Paste with Shikakai powder and water; mix with shampoo or use as extract.</p>
          <h2 className="text-xl font-serif text-salon-ink mt-10 mb-4">Neem (Azadirachta Indica)</h2>
          <p><strong>Benefits:</strong> Antimicrobial and anti-inflammatory, prevents scalp inflammation, controls dandruff, alleviates dryness, strengthens hair. <strong>How to use:</strong> Paste with Neem powder and water; mix with shampoo or use as extract.</p>
          <h2 className="text-xl font-serif text-salon-ink mt-10 mb-4">DIY Indian Shampoo Recipes for Hair Loss</h2>
          <p><strong>1. Bhringraj and Amla Shampoo.</strong> Ingredients: Bhringraj powder, Amla powder, Shikakai powder, Brahmi powder, water, optional lavender essential oil. Boil the herbs, strain, add essential oil, store. <strong>2. Neem and Shikakai Shampoo.</strong> Ingredients: Neem powder, Shikakai powder, Amla powder, water, optional tea tree oil. Same method: boil, strain, add oil, store.</p>
          <h2 className="text-xl font-serif text-salon-ink mt-10 mb-4">Precautions</h2>
          <p>Use consistently for best results. Do a patch test to avoid allergic reactions. Store in the refrigerator and use within a week. Pair with natural conditioners (e.g. coconut oil or yogurt).</p>
          <h2 className="text-xl font-serif text-salon-ink mt-10 mb-4">Recommended Commercial Indian Shampoos</h2>
          <p>Indulekha Bringha Hair Cleanser, Khadi Natural Shikakai Shampoo, Biotique Bio Kelp Protein Shampoo, Forest Essentials Hair Cleanser.</p>
          <p className="mt-10">Natural Indian shampoos can be a holistic way to support hair health and combat hair loss. Experiment with these ingredients and recipes to find what works for you.</p>
        </>
      )}
    </div>
  );
  return content;
};

const Hero = ({ onOpenBooking }: { onOpenBooking: () => void }) => {
  const [currentSlide, setCurrentSlide] = useState(0);
  const slides = [
    "Precision Cuts — Women, Men & Kids",
    "Professional Color — Root, Highlights & More",
    "Perms & Japanese Magic Straight — Lasting Style",
    "Style & Finish — Blow Dry, Upstyle & Makeup"
  ];

  useEffect(() => {
    const timer = setInterval(() => {
      setCurrentSlide((prev) => (prev + 1) % slides.length);
    }, 5000);
    return () => clearInterval(timer);
  }, []);

  return (
    <section className="relative h-screen flex items-center justify-center overflow-hidden">
      <div className="absolute inset-0 z-0">
        <img 
          src="https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?q=80&w=1920&auto=format&fit=crop" 
          alt="Salon Interior" 
          className="w-full h-full object-cover brightness-[0.7]"
          referrerPolicy="no-referrer"
        />
      </div>
      <div className="relative z-10 text-center text-white px-6 max-w-4xl">
        <AnimatePresence mode="wait">
          <motion.h1 
            key={currentSlide}
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -20 }}
            transition={{ duration: 0.8 }}
            className="text-3xl md:text-6xl font-serif mb-12 leading-tight tracking-tight"
          >
            {slides[currentSlide]}
          </motion.h1>
        </AnimatePresence>
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          transition={{ duration: 1, delay: 0.5 }}
        >
          <button type="button" onClick={onOpenBooking} className="gold-button">Book Appointment</button>
        </motion.div>
      </div>
    </section>
  );
};

const Story = () => (
  <section id="story" className="py-32 px-8 bg-white">
    <div id="blog" className="scroll-mt-28" aria-hidden />
    <div className="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
      <motion.div
        initial={{ opacity: 0, x: -30 }}
        whileInView={{ opacity: 1, x: 0 }}
        viewport={{ once: true }}
        transition={{ duration: 1 }}
      >
        <p className="text-[10px] uppercase tracking-[0.3em] text-salon-gold mb-4">Our Story</p>
        <h2 className="text-4xl md:text-5xl font-serif mb-8 leading-tight">
          Change Hair <br /> <span className="italic font-light">&amp; Beauty in Lewisville</span>
        </h2>
        <div className="space-y-6 text-sm md:text-base opacity-80 leading-loose tracking-wide">
          <p>
            Located at The Vista in Lewisville TX, our salon is a sanctuary dedicated to the art of beauty. We believe that hair is the ultimate expression of self, and our mission is to provide personalized styling that enhances your natural beauty.
          </p>
          <p>
            Our team of expert stylists are trained in the latest K-beauty techniques, from the effortless waves of a digital perm to the precision of a down perm. We use only premium products and state-of-the-art equipment to ensure the health and vitality of your hair.
          </p>
        </div>
        <div className="grid grid-cols-2 gap-8 mt-12">
          <div>
            <p className="text-4xl font-serif text-salon-gold mb-2">20+</p>
            <p className="text-[10px] uppercase tracking-widest opacity-60">Years Experience</p>
          </div>
          <div>
            <p className="text-4xl font-serif text-salon-gold mb-2">5k+</p>
            <p className="text-[10px] uppercase tracking-widest opacity-60">Happy Clients</p>
          </div>
        </div>
      </motion.div>
      
      <motion.div
        initial={{ opacity: 0, scale: 0.95 }}
        whileInView={{ opacity: 1, scale: 1 }}
        viewport={{ once: true }}
        transition={{ duration: 1.2 }}
        className="relative aspect-[4/5]"
      >
        <img 
          src="https://images.unsplash.com/photo-1560869713-7d0a29430803?q=80&w=800&auto=format&fit=crop" 
          alt="Salon Interior" 
          className="w-full h-full object-cover"
          referrerPolicy="no-referrer"
        />
        <div className="absolute -bottom-10 -right-10 w-40 h-40 border border-salon-ink/5 -z-10"></div>
      </motion.div>
    </div>
  </section>
);

const SignatureServices = ({ onOpenBooking }: { onOpenBooking: () => void }) => (
  <section className="py-32 px-8 bg-salon-beige">
    <div className="max-w-7xl mx-auto">
      <div className="text-center mb-24">
        <p className="text-[10px] uppercase tracking-[0.3em] text-salon-gold mb-4">Signature Services</p>
        <h2 className="text-4xl font-serif">Cut · Color · Perm · Style</h2>
      </div>

      <div className="space-y-32">
        {/* Cut */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
          <div className="order-2 lg:order-1">
            <p className="text-[10px] uppercase tracking-widest text-salon-gold mb-4">Signature Service</p>
            <h3 className="text-3xl md:text-4xl font-serif mb-8">Cut</h3>
            <p className="text-sm md:text-base opacity-70 leading-loose mb-8">
              Precision cuts for everyone. Our stylists deliver tailored cuts for women, men, and kids—clean lines, modern shapes, and a look that suits you.
            </p>
            <ul className="space-y-4 mb-10">
              {['Women $35+', 'Men $25+', 'Kids $25+'].map((item, i) => (
                <li key={i} className="flex items-center text-sm opacity-80">
                  <Sparkles className="w-4 h-4 text-salon-gold mr-3" />
                  {item}
                </li>
              ))}
            </ul>
            <button type="button" onClick={onOpenBooking} className="gold-button">Book Appointment</button>
          </div>
          <div className="order-1 lg:order-2">
            <img src="https://images.unsplash.com/photo-1562322140-8baeececf3df?q=80&w=800&auto=format&fit=crop" alt="Hair cut" className="w-full aspect-video object-cover shadow-2xl rounded-lg" referrerPolicy="no-referrer" />
          </div>
        </div>

        {/* Color */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
          <div>
            <img src={signatureColor} alt="Hair color" className="w-full aspect-video object-cover shadow-2xl rounded-lg" />
          </div>
          <div>
            <p className="text-[10px] uppercase tracking-widest text-salon-gold mb-4">Color</p>
            <h3 className="text-3xl md:text-4xl font-serif mb-8">Color</h3>
            <p className="text-sm md:text-base opacity-70 leading-loose mb-8">
              From root touch-ups to full highlights, we bring out your best with professional color. Root, manicure, and highlight services for a fresh, vibrant look.
            </p>
            <ul className="space-y-4 mb-10">
              {['Root $80+', 'Manicure $80+', 'Highlight (F) $200+', 'Highlight (M) $150+'].map((item, i) => (
                <li key={i} className="flex items-center text-sm opacity-80">
                  <Sparkles className="w-4 h-4 text-salon-gold mr-3" />
                  {item}
                </li>
              ))}
            </ul>
            <button type="button" onClick={onOpenBooking} className="gold-button">Book Appointment</button>
          </div>
        </div>

        {/* Perm */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
          <div className="order-2 lg:order-1">
            <p className="text-[10px] uppercase tracking-widest text-salon-gold mb-4">Perm</p>
            <h3 className="text-3xl md:text-4xl font-serif mb-8">Perm</h3>
            <p className="text-sm md:text-base opacity-70 leading-loose mb-8">
              From men's iron perm to Japanese magic straight, we offer a range of perm and straightening services. Set/Digital and Magic Setting for lasting waves or sleek, smooth results.
            </p>
            <ul className="space-y-4 mb-10">
              {["Men's Iron Perm $130+", "Basic Women's Perm $100+", 'Set / Digital $200+', 'Magic Setting $250+', 'Japanese Magic Straight $230+'].map((item, i) => (
                <li key={i} className="flex items-center text-sm opacity-80">
                  <Sparkles className="w-4 h-4 text-salon-gold mr-3" />
                  {item}
                </li>
              ))}
            </ul>
            <button type="button" onClick={onOpenBooking} className="gold-button">Book Appointment</button>
          </div>
          <div className="order-1 lg:order-2">
            <img src={signaturePerm} alt="Perm" className="w-full aspect-video object-cover shadow-2xl rounded-lg" />
          </div>
        </div>

        {/* Style */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
          <div>
            <img src="https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?q=80&w=800&auto=format&fit=crop" alt="Style" className="w-full aspect-video object-cover shadow-2xl rounded-lg" referrerPolicy="no-referrer" />
          </div>
          <div>
            <p className="text-[10px] uppercase tracking-widest text-salon-gold mb-4">Style</p>
            <h3 className="text-3xl md:text-4xl font-serif mb-8">Style</h3>
            <p className="text-sm md:text-base opacity-70 leading-loose mb-8">
              Shampoo, blow dry, upstyle, and makeup. Perfect for events, daily refresh, or a full glam look. Let us finish your look with care and precision.
            </p>
            <ul className="space-y-4 mb-10">
              {['Shampoo $20+', 'Blow Dry $35+', 'Upstyle $130+', 'Makeup $150+'].map((item, i) => (
                <li key={i} className="flex items-center text-sm opacity-80">
                  <Sparkles className="w-4 h-4 text-salon-gold mr-3" />
                  {item}
                </li>
              ))}
            </ul>
            <button type="button" onClick={onOpenBooking} className="gold-button">Book Appointment</button>
          </div>
        </div>
      </div>
    </div>
  </section>
);

const ServicesMenu = () => (
  <section id="services" className="py-32 px-8 bg-white">
    <div className="max-w-5xl mx-auto">
      <div className="text-center mb-24">
        <p className="text-[10px] uppercase tracking-[0.3em] text-salon-gold mb-4">Menu</p>
        <h2 className="text-4xl font-serif">Our Services</h2>
        <p className="text-sm text-salon-ink/70 mt-4 max-w-xl mx-auto">Cut, Color, Perm &amp; Style — prices below. Book online or call us.</p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-x-20 gap-y-16">
        {SERVICE_MENU.map((category, idx) => (
          <motion.div 
            key={idx}
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
          >
            <h3 className="text-xs uppercase tracking-[0.4em] mb-8 border-b border-salon-ink/10 pb-4 w-full">
              {category.category}
            </h3>
            <div className="space-y-6">
              {category.items.map((item, i) => (
                <div key={i} className="flex justify-between items-end group cursor-default">
                  <p className="text-sm tracking-wide group-hover:text-salon-gold transition-colors">{item.name}</p>
                  <div className="flex-1 border-b border-dotted border-salon-ink/20 mx-4 mb-1"></div>
                  <p className="text-sm font-medium">{item.price}</p>
                </div>
              ))}
            </div>
          </motion.div>
        ))}
      </div>
    </div>
  </section>
);

// Gallery images used for Menu page cards (WordPress-style)
const MENU_CARD_IMAGES = [gallery1, gallery3, gallery5, gallery7];
const MENU_CARD_BLURBS: Record<string, string> = {
  'CUT': 'Precision cuts for women, men, and kids. Clean lines and modern shapes tailored to you.',
  'COLOR': 'From root touch-ups to full highlights. Professional color for a fresh, vibrant look.',
  'PERM': 'Perms and Japanese magic straight. Lasting waves or sleek, smooth results.',
  'STYLE': 'Shampoo, blow dry, upstyle, and makeup. Perfect for events or a daily refresh.'
};

const MenuView = ({ onBack, onOpenBooking }: { onBack: () => void; onOpenBooking: () => void }) => (
  <div className="min-h-screen bg-salon-beige pt-28 pb-20">
    <div className="max-w-6xl mx-auto px-6 md:px-12">
      <div className="flex items-center gap-4 mb-12">
        <button type="button" onClick={onBack} className="flex items-center gap-2 text-sm uppercase tracking-widest text-salon-ink/70 hover:text-salon-gold transition-colors cursor-pointer">
          <ChevronLeft className="w-4 h-4" /> Back
        </button>
      </div>
      <header className="text-center mb-16">
        <p className="text-[10px] uppercase tracking-[0.3em] text-salon-gold mb-4">Services</p>
        <h1 className="text-4xl md:text-5xl font-serif text-salon-ink">Our Menu</h1>
        <p className="text-sm text-salon-ink/70 mt-4 max-w-xl mx-auto">Cut, Color, Perm &amp; Style. Browse services and book your appointment.</p>
      </header>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-10">
        {SERVICE_MENU.map((cat, idx) => (
          <motion.article
            key={cat.category}
            initial={{ opacity: 0, y: 24 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: idx * 0.08 }}
            className="bg-white rounded-sm overflow-hidden border border-salon-ink/5 shadow-lg hover:shadow-xl transition-shadow"
          >
            <div className="aspect-[4/3] overflow-hidden bg-salon-ink/5">
              <img
                src={MENU_CARD_IMAGES[idx]}
                alt={cat.category}
                className="w-full h-full object-cover"
              />
            </div>
            <div className="p-6 md:p-8">
              <h2 className="text-xl font-serif text-salon-ink border-b border-salon-ink/10 pb-3 mb-4 uppercase tracking-widest">
                {cat.category}
              </h2>
              <p className="text-sm text-salon-ink/70 mb-6 leading-relaxed">
                {MENU_CARD_BLURBS[cat.category]}
              </p>
              <ul className="space-y-3 mb-6">
                {cat.items.map((item, i) => (
                  <li key={i} className="flex justify-between items-baseline text-sm">
                    <span className="text-salon-ink">{item.name}</span>
                    <span className="font-medium text-salon-ink">{item.price}</span>
                  </li>
                ))}
              </ul>
              <button type="button" onClick={onOpenBooking} className="gold-button w-full sm:w-auto">
                Book this service
              </button>
            </div>
          </motion.article>
        ))}
      </div>
    </div>
    <Footer />
  </div>
);

const Gallery = () => (
    <section id="gallery" className="py-32 px-8 bg-salon-beige">
      <div className="max-w-7xl mx-auto">
        <div className="text-center mb-20">
          <h2 className="text-4xl font-serif mb-4">Visual Portfolio</h2>
          <p className="text-[10px] opacity-40 uppercase tracking-widest">A glimpse into our signature K-beauty transformations.</p>
        </div>

        <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
          {GALLERY_ITEMS.map((item, idx) => (
            <motion.a
              key={idx}
              href={item.url}
              target="_blank"
              rel="noopener noreferrer"
              initial={{ opacity: 0 }}
              whileInView={{ opacity: 1 }}
              viewport={{ once: true }}
              transition={{ delay: idx * 0.05 }}
              className="cursor-pointer overflow-hidden aspect-square relative group block"
            >
              <img 
                src={item.img} 
                alt={`Gallery ${idx + 1}`} 
                className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000"
                referrerPolicy="no-referrer"
              />
              <div className="absolute inset-0 bg-salon-ink/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-center justify-center">
                <Instagram className="text-white w-6 h-6" />
              </div>
            </motion.a>
          ))}
        </div>
        <div className="mt-12 flex flex-wrap justify-center gap-6">
          <a href={INSTAGRAM_URL} target="_blank" rel="noopener noreferrer" className="flex items-center justify-center space-x-2 text-[10px] uppercase tracking-widest hover:text-salon-gold transition-colors">
            <Instagram className="w-4 h-4" />
            <span>Follow on Instagram</span>
          </a>
          <a href={FACEBOOK_URL} target="_blank" rel="noopener noreferrer" className="flex items-center justify-center space-x-2 text-[10px] uppercase tracking-widest hover:text-salon-gold transition-colors">
            <Facebook className="w-4 h-4" />
            <span>Follow on Facebook</span>
          </a>
        </div>
      </div>
    </section>
  );

const Testimonials = () => {
  const [active, setActive] = useState(0);

  return (
    <section className="py-32 px-8 bg-salon-ink text-white overflow-hidden">
      <div className="max-w-4xl mx-auto">
        <div className="text-center mb-16">
          <p className="text-[10px] uppercase tracking-widest text-salon-gold mb-4">Client Voices</p>
          <h2 className="text-4xl font-serif">What Our Clients Say</h2>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {TESTIMONIALS.map((t, idx) => (
            <motion.div 
              key={t.id}
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: idx * 0.1 }}
              className="p-8 border border-white/10 hover:border-salon-gold transition-colors"
            >
              <MessageSquare className="w-6 h-6 text-salon-gold mb-6" />
              <p className="text-sm italic mb-6 leading-relaxed opacity-80">"{t.text}"</p>
              <p className="text-[10px] uppercase tracking-widest text-salon-gold">— {t.name}</p>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
};

// Google Apps Script Web App URL for Book Appointment form (override with VITE_GOOGLE_SCRIPT_URL if needed)
const GOOGLE_SCRIPT_URL = import.meta.env.VITE_GOOGLE_SCRIPT_URL || 'https://script.google.com/macros/s/AKfycbx3hh9Cx_6q20r8Al0Soso2Ou8MInz-INHhJ8SpaSQQrP2Pt3oe4_LFzEbXV3OAWaf1pw/exec';

const Booking = () => {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    service: '',
    date: '',
    time: ''
  });
  const [status, setStatus] = useState<'idle' | 'sending' | 'success' | 'error'>('idle');
  const [errorMessage, setErrorMessage] = useState('');

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!GOOGLE_SCRIPT_URL) {
      setErrorMessage('Form is not configured. Please set VITE_GOOGLE_SCRIPT_URL.');
      setStatus('error');
      return;
    }
    setStatus('sending');
    setErrorMessage('');
    try {
      const body = new URLSearchParams({
        name: formData.name,
        email: formData.email,
        phone: formData.phone,
        service: formData.service,
        date: formData.date,
        time: formData.time
      }).toString();
      const res = await fetch(GOOGLE_SCRIPT_URL, {
        method: 'POST',
        mode: 'no-cors',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body
      });
      // no-cors: we can't read response; assume success if no throw
      setStatus('success');
      setFormData({ name: '', email: '', phone: '', service: '', date: '', time: '' });
      setTimeout(() => setStatus('idle'), 5000);
    } catch (err) {
      setStatus('error');
      setErrorMessage(err instanceof Error ? err.message : 'Failed to send. Please try again or call us.');
    }
  };

  return (
    <section id="booking" className="py-32 px-8 bg-white">
      <div className="max-w-2xl mx-auto">
        <div className="text-center mb-16">
          <h2 className="text-4xl font-serif mb-4">Book Appointment</h2>
          <p className="text-[10px] opacity-40 uppercase tracking-[0.3em]">Reserve your transformation</p>
        </div>

        {status === 'success' ? (
          <motion.div 
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            className="text-center py-20 border border-salon-ink/5"
          >
            <h3 className="text-2xl font-serif mb-4">Thank You.</h3>
            <p className="opacity-60 text-sm">Your request has been sent. We will contact you shortly.</p>
          </motion.div>
        ) : (
          <form onSubmit={handleSubmit} className="space-y-10">
            {status === 'error' && (
              <p className="text-sm text-red-600 bg-red-50 border border-red-200 px-4 py-2">{errorMessage}</p>
            )}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-10">
              <div className="space-y-2">
                <label className="text-[10px] uppercase tracking-widest opacity-40">Name</label>
                <input 
                  type="text" 
                  required
                  value={formData.name}
                  className="w-full border-b border-salon-ink/10 py-2 focus:border-salon-gold outline-none transition-colors bg-transparent"
                  onChange={(e) => setFormData({...formData, name: e.target.value})}
                />
              </div>
              <div className="space-y-2">
                <label className="text-[10px] uppercase tracking-widest opacity-40">Email</label>
                <input 
                  type="email" 
                  required
                  value={formData.email}
                  className="w-full border-b border-salon-ink/10 py-2 focus:border-salon-gold outline-none transition-colors bg-transparent"
                  onChange={(e) => setFormData({...formData, email: e.target.value})}
                />
              </div>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-10">
              <div className="space-y-2">
                <label className="text-[10px] uppercase tracking-widest opacity-40">Phone</label>
                <input 
                  type="tel" 
                  required
                  value={formData.phone}
                  className="w-full border-b border-salon-ink/10 py-2 focus:border-salon-gold outline-none transition-colors bg-transparent"
                  onChange={(e) => setFormData({...formData, phone: e.target.value})}
                />
              </div>
              <div className="space-y-2">
                <label className="text-[10px] uppercase tracking-widest opacity-40">Service</label>
                <select 
                  required
                  value={formData.service}
                  className="w-full border-b border-salon-ink/10 py-2 focus:border-salon-gold outline-none transition-colors bg-transparent"
                  onChange={(e) => setFormData({...formData, service: e.target.value})}
                >
                  <option value="">Select</option>
                  <option value="Cut">Cut</option>
                  <option value="Color">Color</option>
                  <option value="Perm">Perm</option>
                  <option value="Style">Style</option>
                </select>
              </div>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-10">
              <div className="space-y-2">
                <label className="text-[10px] uppercase tracking-widest opacity-40">Date</label>
                <input 
                  type="date" 
                  required
                  value={formData.date}
                  className="w-full border-b border-salon-ink/10 py-2 focus:border-salon-gold outline-none transition-colors bg-transparent"
                  onChange={(e) => setFormData({...formData, date: e.target.value})}
                />
              </div>
              <div className="space-y-2">
                <label className="text-[10px] uppercase tracking-widest opacity-40">Time</label>
                <input 
                  type="time" 
                  required
                  value={formData.time}
                  className="w-full border-b border-salon-ink/10 py-2 focus:border-salon-gold outline-none transition-colors bg-transparent"
                  onChange={(e) => setFormData({...formData, time: e.target.value})}
                />
              </div>
            </div>
            <button type="submit" disabled={status === 'sending'} className="w-full bg-salon-ink text-white py-4 text-[10px] uppercase tracking-[0.4em] hover:bg-salon-gold transition-all duration-500 disabled:opacity-60 disabled:cursor-not-allowed">
              {status === 'sending' ? 'Sending…' : 'Send Request'}
            </button>
          </form>
        )}
      </div>
    </section>
  );
};

const Contact = () => (
  <section id="contact" className="py-32 px-8 bg-salon-beige">
    <div className="max-w-7xl mx-auto">
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-20">
        <div>
          <h2 className="text-4xl font-serif mb-16">Contact Us</h2>
          <div className="space-y-12">
            <div>
              <p className="text-[10px] uppercase tracking-widest text-salon-gold mb-4 flex items-center">
                <MapPin className="w-3 h-3 mr-2" /> Location
              </p>
              <p className="text-sm opacity-70 leading-relaxed">
                {LOCATION_NAME}<br />
                {LOCATION_ADDRESS}
              </p>
            </div>
            <div>
              <p className="text-[10px] uppercase tracking-widest text-salon-gold mb-4 flex items-center">
                <Clock className="w-3 h-3 mr-2" /> Hours
              </p>
              <ul className="text-sm opacity-70 leading-loose space-y-1">
                {LOCATION_HOURS.map((line, i) => <li key={i}>{line}</li>)}
              </ul>
            </div>
            <div>
              <p className="text-[10px] uppercase tracking-widest text-salon-gold mb-4 flex items-center">
                <Phone className="w-3 h-3 mr-2" /> Contact
              </p>
              <div className="text-sm opacity-70 leading-relaxed space-y-2">
                <p>Phone: {LOCATION_PHONE}</p>
                <p>
                  <a href={TAWK_CHAT_URL} target="_blank" rel="noopener noreferrer" className="inline-flex items-center gap-2 hover:text-salon-gold transition-colors cursor-pointer">
                    <MessageSquare className="w-3 h-3" /> Live chat
                  </a>
                </p>
              </div>
            </div>
          </div>
        </div>

        <div className="relative w-full mt-8 overflow-hidden border border-salon-ink/10 bg-salon-ink/5 grayscale hover:grayscale-0 opacity-95 hover:opacity-100 transition-all duration-500" style={{ height: 500 }}>
          <iframe 
            title="Change Hair & Beauty - Contact"
            src={MAP_EMBED_URL}
            width="100%" 
            height="100%" 
            style={{ border: 0, position: 'absolute', left: 0, top: 0, width: '100%', height: '100%', display: 'block' }}
            allowFullScreen 
            referrerPolicy="no-referrer-when-downgrade"
          />
        </div>
      </div>
    </div>
  </section>
);

const Footer = () => (
  <footer className="bg-white py-20 px-8 border-t border-salon-ink/5">
    <div className="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-12">
      <a href="#" className="text-2xl font-serif tracking-[0.3em] uppercase flex flex-col items-center">
        <span className="text-salon-ink">CHANGE HAIR</span>
        <span className="text-[8px] tracking-[0.4em] -mt-1 opacity-50">{' & BEAUTY'}</span>
      </a>
      
      <div className="flex space-x-12 items-center">
        <a href={TAWK_CHAT_URL} target="_blank" rel="noopener noreferrer" className="hover:text-salon-gold transition-colors" title="Live chat"><MessageSquare className="w-5 h-5" /></a>
        <a href={INSTAGRAM_URL} target="_blank" rel="noopener noreferrer" className="hover:text-salon-gold transition-colors"><Instagram className="w-5 h-5" /></a>
        <a href={FACEBOOK_URL} target="_blank" rel="noopener noreferrer" className="hover:text-salon-gold transition-colors"><Facebook className="w-5 h-5" /></a>
      </div>

      <p className="text-[10px] uppercase tracking-[0.2em] opacity-40">
        Copyright &copy; {new Date().getFullYear()}, Change Hair &amp; Beauty
      </p>
    </div>
  </footer>
);

// --- Booking Modal (4 steps) ---
const BookingModal = ({ isOpen, onClose }: { isOpen: boolean; onClose: () => void }) => {
  const [step, setStep] = useState(1);
  const [expandedCategoryId, setExpandedCategoryId] = useState<string | null>(null);
  const [selectedService, setSelectedService] = useState<{ name: string; price: string; categoryName: string } | null>(null);
  const [calendarMonth, setCalendarMonth] = useState(() => { const d = new Date(); return new Date(d.getFullYear(), d.getMonth(), 1); });
  const [selectedDate, setSelectedDate] = useState<Date | null>(null);
  const [selectedTime, setSelectedTime] = useState<string | null>(null);
  const [customerTab, setCustomerTab] = useState<'new' | 'existing'>('new');
  const [form, setForm] = useState({ firstName: '', lastName: '', phone: '', email: '', password: '', confirmPassword: '', comments: '' });
  const [submitted, setSubmitted] = useState(false);
  const [sending, setSending] = useState(false);
  const [submitError, setSubmitError] = useState('');

  useEffect(() => { if (isOpen) { setStep(1); setExpandedCategoryId(null); setSelectedService(null); setSelectedDate(null); setSelectedTime(null); setForm({ firstName: '', lastName: '', phone: '', email: '', password: '', confirmPassword: '', comments: '' }); setSubmitted(false); setSubmitError(''); } }, [isOpen]);

  const handleModalSubmit = async () => {
    if (!GOOGLE_SCRIPT_URL) { setSubmitError('Form is not configured.'); return; }
    setSending(true);
    setSubmitError('');
    const dateStr = selectedDate ? `${selectedDate.getFullYear()}-${String(selectedDate.getMonth() + 1).padStart(2, '0')}-${String(selectedDate.getDate()).padStart(2, '0')}` : '';
    const name = [form.firstName, form.lastName].filter(Boolean).join(' ');
    try {
      const body = new URLSearchParams({
        name: name || '—',
        email: form.email || '—',
        phone: form.phone || '—',
        service: selectedService?.name || selectedService?.categoryName || '—',
        date: dateStr,
        time: selectedTime || '—'
      }).toString();
      await fetch(GOOGLE_SCRIPT_URL, {
        method: 'POST',
        mode: 'no-cors',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body
      });
      setSubmitted(true);
    } catch (err) {
      setSubmitError(err instanceof Error ? err.message : 'Failed to send. Please try again or call us.');
    } finally {
      setSending(false);
    }
  };

  const handleServicePick = (cat: { id: string; name: string; services: { name: string; price: string }[] }, svc: { name: string; price: string }) => {
    setSelectedService({ ...svc, categoryName: cat.name });
  };

  const getDaysInMonth = (d: Date) => {
    const year = d.getFullYear(), month = d.getMonth();
    const first = new Date(year, month, 1).getDay();
    const days = new Date(year, month + 1, 0).getDate();
    const today = new Date();
    const pad: number[] = [];
    for (let i = 0; i < first; i++) pad.push(-1);
    for (let i = 1; i <= days; i++) pad.push(i);
    return { pad, year, month, today };
  };

  const isAvailable = (year: number, month: number, day: number) => {
    const d = new Date(year, month, day);
    const t = new Date();
    t.setHours(0, 0, 0, 0);
    return d >= t && d.getDay() !== 0;
  };

  const timeSlotLabel = (start: string, i: number) => {
    const next = TIME_SLOTS[i + 1] || '6:30 pm';
    return `${start} - ${next}`;
  };

  const formatDate = (d: Date | null) => d ? `${String(d.getMonth() + 1).padStart(2, '0')}/${String(d.getDate()).padStart(2, '0')}/${d.getFullYear()}` : '';
  const formatDateShort = (d: Date | null) => d ? `${String(d.getMonth() + 1).padStart(2, '0')}/${String(d.getDate()).padStart(2, '0')}/${d.getFullYear()}` : '';

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-salon-ink/60 backdrop-blur-sm" onClick={(e) => e.target === e.currentTarget && onClose()}>
      <motion.div initial={{ opacity: 0, scale: 0.96 }} animate={{ opacity: 1, scale: 1 }} exit={{ opacity: 0, scale: 0.96 }} className="bg-white max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-salon-ink/10" onClick={e => e.stopPropagation()}>
        <div className="sticky top-0 bg-white border-b border-salon-ink/10 px-6 py-4 flex justify-between items-center">
          <h2 className="text-xl font-serif text-salon-ink">
            {step === 1 && 'Available Services'}
            {step === 2 && 'Date & Time Selection'}
            {step === 3 && 'Customer Information'}
            {step === 4 && 'Verify Order Details'}
          </h2>
          <button type="button" onClick={onClose} className="p-2 hover:bg-salon-ink/5 rounded transition-colors"><X className="w-5 h-5" /></button>
        </div>

        <div className="p-6">
          {submitted ? (
            <div className="py-16 text-center">
              <h3 className="text-2xl font-serif text-salon-ink mb-2">Thank you.</h3>
              <p className="text-sm text-salon-ink/70">Your appointment request has been submitted. We will contact you shortly.</p>
              <button type="button" onClick={onClose} className="gold-button mt-8">Close</button>
            </div>
          ) : (
            <>
              {/* Step 1: Available Services */}
              {step === 1 && (
                <div className="space-y-3">
                  {BOOKING_CATEGORIES.map((cat) => (
                    <div key={cat.id}>
                      <button type="button" onClick={() => setExpandedCategoryId(expandedCategoryId === cat.id ? null : cat.id)} className="w-full flex items-center justify-between p-4 rounded-lg bg-salon-beige/60 border border-salon-ink/5 hover:border-salon-gold/30 transition-colors text-left">
                        <span className="flex items-center gap-3">
                          <Scissors className="w-5 h-5 text-salon-gold" />
                          <span className="font-medium text-salon-ink">{cat.name}</span>
                        </span>
                        <span className="text-[10px] uppercase tracking-widest text-salon-ink/50">{cat.services.length} Services</span>
                      </button>
                      {expandedCategoryId === cat.id && (
                        <div className="mt-2 pl-4 space-y-2 border-l-2 border-salon-gold/30">
                          {cat.services.map((svc) => (
                            <button key={svc.name} type="button" onClick={() => handleServicePick(cat, svc)} className={`w-full flex justify-between items-center py-3 px-3 rounded text-left text-sm transition-colors ${selectedService?.name === svc.name && selectedService?.categoryName === cat.name ? 'bg-salon-gold/20 text-salon-ink border border-salon-gold/50' : 'hover:bg-salon-beige/50'}`}>
                              <span>{svc.name}</span>
                              <span className="text-salon-gold text-[10px]">{svc.price}</span>
                            </button>
                          ))}
                        </div>
                      )}
                    </div>
                  ))}
                </div>
              )}

              {/* Step 2: Date & Time */}
              {step === 2 && (
                <div>
                  <div>
                    <p className="text-[10px] uppercase tracking-widest text-salon-gold mb-2">{calendarMonth.toLocaleString('default', { month: 'long' })} {calendarMonth.getFullYear()}</p>
                    <div className="grid grid-cols-7 gap-1 text-center text-[10px] uppercase text-salon-ink/50 mb-2">
                      {['S','M','T','W','T','F','S'].map((d,i)=> <span key={i}>{d}</span>)}
                    </div>
                    {(()=>{ const { pad, year, month, today } = getDaysInMonth(calendarMonth); return (
                      <div className="grid grid-cols-7 gap-1">
                        {pad.map((day, i) => (
                          day === -1 ? <div key={i} /> : (
                            <button key={i} type="button" disabled={!isAvailable(year, month, day)} onClick={()=> setSelectedDate(new Date(year, month, day))} className={`aspect-square text-sm rounded ${!isAvailable(year, month, day) ? 'text-salon-ink/30 cursor-not-allowed' : selectedDate?.getDate() === day && selectedDate?.getMonth() === month ? 'bg-salon-gold text-white' : 'hover:bg-salon-beige'}`}>
                              {day}
                            </button>
                          )
                        ))}
                      </div>
                    )})()}
                    <div className="flex gap-2 mt-4">
                      <button type="button" onClick={() => setCalendarMonth(new Date(calendarMonth.getFullYear(), calendarMonth.getMonth() - 1))} className="p-2 border border-salon-ink/10 hover:border-salon-gold"><ChevronLeft className="w-4 h-4" /></button>
                      <button type="button" onClick={() => setCalendarMonth(new Date(calendarMonth.getFullYear(), calendarMonth.getMonth() + 1))} className="p-2 border border-salon-ink/10 hover:border-salon-gold"><ChevronRight className="w-4 h-4" /></button>
                    </div>
                    {selectedDate && (
                      <p className="text-[10px] uppercase tracking-widest text-salon-gold mt-4">Pick a slot for {formatDateShort(selectedDate)}</p>
                    )}
                    <div className="grid grid-cols-2 gap-2 mt-2 max-h-48 overflow-y-auto">
                      {TIME_SLOTS.slice(0, -1).map((start, i) => {
                        const label = timeSlotLabel(start, i);
                        return (
                          <button key={label} type="button" onClick={() => setSelectedTime(label)} className={`py-2 px-3 text-left text-sm rounded border transition-colors ${selectedTime === label ? 'bg-salon-gold text-white border-salon-gold' : 'border-salon-ink/10 hover:border-salon-gold/50 bg-salon-beige/30'}`}>
                            {label}
                          </button>
                        );
                      })}
                    </div>
                  </div>
                </div>
              )}

              {/* Step 3: Customer Information */}
              {step === 3 && (
                <div>
                  <div>
                    <div className="flex gap-6 border-b border-salon-ink/10 mb-6">
                      <button type="button" onClick={() => setCustomerTab('new')} className={`pb-2 text-sm font-medium border-b-2 transition-colors ${customerTab === 'new' ? 'border-salon-gold text-salon-ink' : 'border-transparent text-salon-ink/50'}`}>New Customer</button>
                      <button type="button" onClick={() => setCustomerTab('existing')} className={`pb-2 text-sm font-medium border-b-2 transition-colors ${customerTab === 'existing' ? 'border-salon-gold text-salon-ink' : 'border-transparent text-salon-ink/50'}`}>Already have an account?</button>
                    </div>
                    {customerTab === 'new' ? (
                      <div className="space-y-4">
                        <div className="grid grid-cols-2 gap-4">
                          <div><label className="block text-[10px] uppercase tracking-widest text-salon-ink/60 mb-1">First Name</label><input type="text" value={form.firstName} onChange={e=> setForm(f=> ({...f, firstName: e.target.value}))} className="w-full border-b border-salon-ink/20 py-2 focus:border-salon-gold outline-none bg-transparent" /></div>
                          <div><label className="block text-[10px] uppercase tracking-widest text-salon-ink/60 mb-1">Last Name</label><input type="text" value={form.lastName} onChange={e=> setForm(f=> ({...f, lastName: e.target.value}))} className="w-full border-b border-salon-ink/20 py-2 focus:border-salon-gold outline-none bg-transparent" /></div>
                        </div>
                        <div><label className="block text-[10px] uppercase tracking-widest text-salon-ink/60 mb-1">Phone</label><input type="tel" value={form.phone} onChange={e=> setForm(f=> ({...f, phone: e.target.value}))} placeholder="(201) 555-0123" className="w-full border-b border-salon-ink/20 py-2 focus:border-salon-gold outline-none bg-transparent" /></div>
                        <div><label className="block text-[10px] uppercase tracking-widest text-salon-ink/60 mb-1">Email Address</label><input type="email" value={form.email} onChange={e=> setForm(f=> ({...f, email: e.target.value}))} className="w-full border-b border-salon-ink/20 py-2 focus:border-salon-gold outline-none bg-transparent" /></div>
                        <div><label className="block text-[10px] uppercase tracking-widest text-salon-ink/60 mb-1">Password</label><input type="password" value={form.password} onChange={e=> setForm(f=> ({...f, password: e.target.value}))} className="w-full border-b border-salon-ink/20 py-2 focus:border-salon-gold outline-none bg-transparent" /></div>
                        <div><label className="block text-[10px] uppercase tracking-widest text-salon-ink/60 mb-1">Confirm Password</label><input type="password" value={form.confirmPassword} onChange={e=> setForm(f=> ({...f, confirmPassword: e.target.value}))} className="w-full border-b border-salon-ink/20 py-2 focus:border-salon-gold outline-none bg-transparent" /></div>
                        <div><label className="block text-[10px] uppercase tracking-widest text-salon-ink/60 mb-1">Add Comments</label><textarea value={form.comments} onChange={e=> setForm(f=> ({...f, comments: e.target.value}))} rows={3} className="w-full border border-salon-ink/20 py-2 px-3 focus:border-salon-gold outline-none bg-transparent resize-none" /></div>
                        <div className="relative my-6"><span className="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white px-2 text-[10px] uppercase text-salon-gold">Or</span><hr className="border-salon-ink/10" /></div>
                        <button type="button" onClick={() => window.open('https://accounts.google.com/', '_blank')} className="w-full border border-salon-ink/20 py-3 text-sm uppercase tracking-widest hover:border-salon-gold transition-colors flex items-center justify-center gap-2"><span className="font-semibold">G</span> Continue with Google</button>
                      </div>
                    ) : (
                      <p className="text-sm text-salon-ink/70">Sign in with your account to view existing appointments and faster booking.</p>
                    )}
                  </div>
                </div>
              )}

              {/* Step 4: Verify */}
              {step === 4 && (
                <div className="space-y-6">
                  {submitError && <p className="text-sm text-red-600 bg-red-50 border border-red-200 px-3 py-2">{submitError}</p>}
                  <div><p className="text-[10px] uppercase tracking-widest text-salon-gold mb-1">Service</p><p className="font-medium">{selectedService?.name}</p></div>
                  <div><p className="text-[10px] uppercase tracking-widest text-salon-gold mb-1">Date & Time</p><p className="font-medium">{formatDate(selectedDate)}, {selectedTime}</p></div>
                  <div><p className="text-[10px] uppercase tracking-widest text-salon-gold mb-1">Agent</p><div className="flex items-center gap-2"><div className="w-10 h-10 rounded-full bg-salon-gold/20 flex items-center justify-center text-salon-gold font-serif font-medium">{AGENT_NAME.split(' ').map(n=>n[0]).join('')}</div><span className="font-medium">{AGENT_NAME}</span></div></div>
                  <div><p className="text-[10px] uppercase tracking-widest text-salon-gold mb-1">Location</p><p className="font-medium flex items-center gap-2"><MapPin className="w-4 h-4 text-salon-gold" /> {LOCATION_NAME}</p><p className="text-sm text-salon-ink/70">{LOCATION_ADDRESS}</p></div>
                  <div><p className="text-[10px] uppercase tracking-widest text-salon-gold mb-1">Customer</p><div className="flex items-center gap-2"><div className="w-10 h-10 rounded-full bg-salon-gold/20 flex items-center justify-center text-salon-gold font-serif font-medium">{(form.firstName[0]||'')+(form.lastName[0]||'') || '?'}</div><span className="font-medium">{form.firstName} {form.lastName}</span></div><p className="text-sm text-salon-ink/70">{form.email || '—'}</p></div>
                </div>
              )}

              <div className="flex justify-between items-center mt-8 pt-6 border-t border-salon-ink/10">
                <button type="button" onClick={() => step > 1 ? setStep(s => s - 1) : onClose()} className="flex items-center gap-2 text-salon-ink/70 hover:text-salon-ink text-sm uppercase tracking-widest"><ChevronLeft className="w-4 h-4" /> Back</button>
                {step < 4 ? (
                  <button type="button" onClick={() => setStep(s => s + 1)} disabled={(step === 1 && !selectedService) || (step === 2 && (!selectedDate || !selectedTime))} className="gold-button py-3 px-6">Next</button>
                ) : (
                  <button type="button" onClick={handleModalSubmit} disabled={sending} className="gold-button py-3 px-6 flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                    {sending ? 'Sending…' : <>Submit <ArrowRight className="w-4 h-4" /></>}
                  </button>
                )}
              </div>
            </>
          )}
        </div>
      </motion.div>
    </div>
  );
};

export default function App() {
  const [showDashboard, setShowDashboard] = useState(false);
  const [showBlog, setShowBlog] = useState(false);
  const [showMenu, setShowMenu] = useState(false);
  const [bookingModalOpen, setBookingModalOpen] = useState(false);

  const handleBack = () => { setShowDashboard(false); setShowBlog(false); setShowMenu(false); };
  const handleMenuClick = () => { setShowMenu(true); setShowDashboard(false); setShowBlog(false); };

  return (
    <div className="min-h-screen selection:bg-salon-gold/20">
      <Navbar
        onDashboardClick={() => setShowDashboard(true)}
        showDashboard={showDashboard}
        onMenuClick={handleMenuClick}
        showMenu={showMenu}
        onBack={handleBack}
        onOpenBooking={() => setBookingModalOpen(true)}
      />
      {showDashboard ? (
        <ClientDashboard onOpenBooking={() => setBookingModalOpen(true)} />
      ) : showMenu ? (
        <MenuView onBack={handleBack} onOpenBooking={() => setBookingModalOpen(true)} />
      ) : showBlog ? (
        <BlogView />
      ) : (
        <>
          <Hero onOpenBooking={() => setBookingModalOpen(true)} />
      <Story />
          <SignatureServices onOpenBooking={() => setBookingModalOpen(true)} />
          <ServicesMenu onOpenBooking={() => setBookingModalOpen(true)} />
          <Gallery onOpenBooking={() => setBookingModalOpen(true)} />
      <Testimonials />
      <Booking />
      <Contact />
      <Footer />
        </>
      )}
      <BookingModal isOpen={bookingModalOpen} onClose={() => setBookingModalOpen(false)} />
    </div>
  );
}
