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
  Heart
} from 'lucide-react';

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

// --- Data ---
const SERVICE_MENU: ServiceCategory[] = [
  {
    category: 'HAIRCUT & PERM',
    items: [
      { name: 'Director Cut', price: '$95' },
      { name: 'Senior Stylist Cut', price: '$85' },
      { name: "Men's Cut", price: '$75' },
      { name: 'Digital Perm', price: '$250+' },
      { name: 'Down Perm', price: '$60+' },
    ]
  },
  {
    category: 'HAIR COLORING',
    items: [
      { name: 'Single Process', price: '$120+' },
      { name: 'Double Process', price: '$220+' },
      { name: 'Balayage / Ombre', price: '$250+' },
      { name: 'Highlight (Full)', price: '$200+' },
    ]
  },
  {
    category: 'HAIR TREATMENTS',
    items: [
      { name: 'Keratin Treatment', price: '$250+' },
      { name: 'Milbon Treatment', price: '$80+' },
      { name: 'Moisture Mask', price: '$40+' },
    ]
  },
  {
    category: 'HEAD SPA',
    items: [
      { name: 'Signature Head Spa', price: '$120' },
      { name: 'Scalp Deep Cleansing', price: '$80' },
      { name: 'Stress Relief Massage', price: '$60' },
    ]
  },
  {
    category: 'HAIR EXTENSIONS',
    items: [
      { name: 'Premium Extensions', price: 'Consultation' },
      { name: 'Volume Boost', price: 'Consultation' },
    ]
  },
  {
    category: 'SCALP MICRO PIGMENTATION (SMP)',
    items: [
      { name: 'Hairline Restoration', price: 'Consultation' },
      { name: 'Density Fill', price: 'Consultation' },
      { name: 'Scar Camouflage', price: 'Consultation' },
    ]
  }
];

const TESTIMONIALS: Testimonial[] = [
  { id: 1, name: "Sarah J.", text: "The best digital perm I've ever had! It looks so natural and my hair feels healthier than before." },
  { id: 2, name: "David K.", text: "Finally found a place that knows how to do a proper down perm. Cleanest fade and styling in Plano." },
  { id: 3, name: "Michelle L.", text: "The head spa was incredibly relaxing. It's my monthly self-care ritual now. Highly recommend!" },
  { id: 4, name: "Grace P.", text: "Expert stylists who really listen. My ash tone color came out exactly how I wanted it." },
  { id: 5, name: "Kevin T.", text: "Modern, clean, and professional. The atmosphere is just like salon in Korea." },
  { id: 6, name: "Emily W.", text: "Amazing service from start to finish. The online booking is so convenient!" },
];

const GALLERY_IMAGES = [
  'https://images.unsplash.com/photo-1562322140-8baeececf3df?q=80&w=800&auto=format&fit=crop',
  'https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?q=80&w=800&auto=format&fit=crop',
  'https://images.unsplash.com/photo-1560869713-7d0a29430803?q=80&w=800&auto=format&fit=crop',
  'https://images.unsplash.com/photo-1492106087820-71f1a00d2b11?q=80&w=800&auto=format&fit=crop',
  'https://images.unsplash.com/photo-1595476108010-b4d1f80d915d?q=80&w=800&auto=format&fit=crop',
  'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?q=80&w=800&auto=format&fit=crop',
];

// --- Components ---

const Navbar = () => {
  const [isScrolled, setIsScrolled] = useState(false);
  const [isMenuOpen, setIsMenuOpen] = useState(false);

  useEffect(() => {
    const handleScroll = () => setIsScrolled(window.scrollY > 50);
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  return (
    <nav className={`fixed w-full z-50 transition-all duration-700 ${isScrolled ? 'bg-white/95 backdrop-blur-sm py-4 shadow-sm' : 'bg-transparent py-8'}`}>
      <div className="max-w-7xl mx-auto px-8 flex justify-between items-center">
        <a href="#" className="text-xl font-serif tracking-[0.3em] uppercase flex flex-col items-center">
          <span className="text-salon-ink">PIKA PIKA</span>
          <span className="text-[8px] tracking-[0.5em] -mt-1 opacity-50">HAIR SALON</span>
        </a>
        
        <div className="hidden md:flex space-x-12 items-center">
          {['Story', 'Services', 'Gallery', 'Contact'].map((item) => (
            <a key={item} href={`#${item.toLowerCase()}`} className="text-[10px] uppercase tracking-[0.2em] hover:text-salon-gold transition-colors">
              {item}
            </a>
          ))}
          <a href="#booking" className="text-[10px] uppercase tracking-[0.2em] border border-salon-ink px-6 py-2 hover:bg-salon-ink hover:text-white transition-all">
            Book Appointment
          </a>
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
            {['Story', 'Services', 'Gallery', 'Contact'].map((item) => (
              <a 
                key={item} 
                href={`#${item.toLowerCase()}`} 
                className="text-lg uppercase tracking-[0.3em] font-serif"
                onClick={() => setIsMenuOpen(false)}
              >
                {item}
              </a>
            ))}
            <a 
              href="#booking" 
              className="gold-button !mt-12"
              onClick={() => setIsMenuOpen(false)}
            >
              Book Now
            </a>
          </motion.div>
        )}
      </AnimatePresence>
    </nav>
  );
};

const Hero = () => {
  const [currentSlide, setCurrentSlide] = useState(0);
  const slides = [
    "Transform Your Hair with Our Signature Keratin Treatments",
    "Stay Trendy with the Latest Korean Hair Styles",
    "Experience Ultimate Relaxation with Our Head Spa Services",
    "Add Volume and Length with Our Premium Extensions",
    "Achieve a Natural Look with Our Expert SMP Services"
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
          <a href="#booking" className="gold-button">Book Appointment</a>
        </motion.div>
      </div>
    </section>
  );
};

const Story = () => (
  <section id="story" className="py-32 px-8 bg-white">
    <div className="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
      <motion.div
        initial={{ opacity: 0, x: -30 }}
        whileInView={{ opacity: 1, x: 0 }}
        viewport={{ once: true }}
        transition={{ duration: 1 }}
      >
        <p className="text-[10px] uppercase tracking-[0.3em] text-salon-gold mb-4">Our Story</p>
        <h2 className="text-4xl md:text-5xl font-serif mb-8 leading-tight">
          Bringing Korean <br /> <span className="italic font-light">Finest Styling to Plano</span>
        </h2>
        <div className="space-y-6 text-sm md:text-base opacity-80 leading-loose tracking-wide">
          <p>
            Located just outside H-Mart Plano TX, our salon is a sanctuary dedicated to the art of Korean beauty. We believe that hair is the ultimate expression of self, and our mission is to provide personalized styling that enhances your natural beauty.
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

const SignatureServices = () => (
  <section className="py-32 px-8 bg-salon-beige">
    <div className="max-w-7xl mx-auto">
      <div className="text-center mb-24">
        <p className="text-[10px] uppercase tracking-[0.3em] text-salon-gold mb-4">Signature Services</p>
        <h2 className="text-4xl font-serif">Excellence in K-Beauty</h2>
      </div>

      <div className="space-y-32">
        {/* Digital Perm */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
          <div className="order-2 lg:order-1">
            <p className="text-[10px] uppercase tracking-widest text-salon-gold mb-4">Signature Service</p>
            <h3 className="text-3xl md:text-4xl font-serif mb-8">Korean Digital Perm</h3>
            <p className="text-sm md:text-base opacity-70 leading-loose mb-8">
              Our signature Digital Perm creates soft, natural-looking waves that are incredibly easy to manage. Unlike traditional perms, this technique uses thermal heat to "set" the curl, resulting in a look that is more defined when dry.
            </p>
            <ul className="space-y-4 mb-10">
              {['Natural, bouncy waves', 'Long-lasting results (3-6 months)', 'Minimal daily styling required', 'Tailored to your hair texture'].map((item, i) => (
                <li key={i} className="flex items-center text-sm opacity-80">
                  <Sparkles className="w-4 h-4 text-salon-gold mr-3" />
                  {item}
                </li>
              ))}
            </ul>
            <a href="#booking" className="gold-button">Book Appointment</a>
          </div>
          <div className="order-1 lg:order-2">
            <img src="https://images.unsplash.com/photo-1562322140-8baeececf3df?q=80&w=800&auto=format&fit=crop" alt="Digital Perm" className="w-full aspect-video object-cover shadow-2xl" referrerPolicy="no-referrer" />
          </div>
        </div>

        {/* Head Spa */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
          <div>
            <img src="https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?q=80&w=800&auto=format&fit=crop" alt="Head Spa" className="w-full aspect-video object-cover shadow-2xl" referrerPolicy="no-referrer" />
          </div>
          <div>
            <p className="text-[10px] uppercase tracking-widest text-salon-gold mb-4">Wellness & Care</p>
            <h3 className="text-3xl md:text-4xl font-serif mb-8">Premium Scalp Care</h3>
            <p className="text-sm md:text-base opacity-70 leading-loose mb-8">
              Indulge in our signature Head Spa treatment, a holistic approach to scalp health and relaxation. We combine deep cleansing, specialized massage techniques, and nutrient-rich treatments to rejuvenate your scalp and hair from the roots.
            </p>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
              <div>
                <h4 className="font-serif text-lg mb-2">Deep Cleansing</h4>
                <p className="text-xs opacity-60">Removes impurities and excess oil for a healthy scalp environment.</p>
              </div>
              <div>
                <h4 className="font-serif text-lg mb-2">Stress Relief</h4>
                <p className="text-xs opacity-60">Relaxing massage techniques to reduce tension and improve circulation.</p>
              </div>
            </div>
            <a href="#booking" className="gold-button">Book Appointment</a>
          </div>
        </div>

        {/* SMP */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
          <div className="order-2 lg:order-1">
            <p className="text-[10px] uppercase tracking-widest text-salon-gold mb-4">Advanced Solutions</p>
            <h3 className="text-3xl md:text-4xl font-serif mb-8">Scalp Micro Pigmentation (SMP)</h3>
            <p className="text-sm md:text-base opacity-70 leading-loose mb-8">
              SMP is a non-surgical, life-changing treatment where natural pigments are applied at the epidermal level of the scalp to replicate the natural appearance of real hair follicles. Perfect for thinning hair, receding hairlines, or scalp scars.
            </p>
            <ul className="space-y-4 mb-10">
              {['Immediate results with a natural, fuller look', 'Safe, non-invasive, and minimal downtime', 'Customized pigment matching for a seamless blend'].map((item, i) => (
                <li key={i} className="flex items-center text-sm opacity-80">
                  <ShieldCheck className="w-4 h-4 text-salon-gold mr-3" />
                  {item}
                </li>
              ))}
            </ul>
            <a href="#booking" className="gold-button">Book Appointment</a>
          </div>
          <div className="order-1 lg:order-2">
            <img src="https://images.unsplash.com/photo-1595476108010-b4d1f80d915d?q=80&w=800&auto=format&fit=crop" alt="SMP" className="w-full aspect-video object-cover shadow-2xl" referrerPolicy="no-referrer" />
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

const Gallery = () => {
  const [selectedImg, setSelectedImg] = useState<string | null>(null);

  return (
    <section id="gallery" className="py-32 px-8 bg-salon-beige">
      <div className="max-w-7xl mx-auto">
        <div className="text-center mb-20">
          <h2 className="text-4xl font-serif mb-4">Visual Portfolio</h2>
          <p className="text-[10px] opacity-40 uppercase tracking-widest">A glimpse into our signature K-beauty transformations.</p>
        </div>

        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          {GALLERY_IMAGES.map((img, idx) => (
            <motion.div 
              key={idx}
              initial={{ opacity: 0 }}
              whileInView={{ opacity: 1 }}
              viewport={{ once: true }}
              transition={{ delay: idx * 0.1 }}
              className="cursor-pointer overflow-hidden aspect-square relative group"
              onClick={() => setSelectedImg(img)}
            >
              <img 
                src={img} 
                alt={`Gallery ${idx}`} 
                className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000"
                referrerPolicy="no-referrer"
              />
              <div className="absolute inset-0 bg-salon-ink/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-center justify-center">
                <Instagram className="text-white w-6 h-6" />
              </div>
            </motion.div>
          ))}
        </div>
        <div className="mt-12 text-center">
          <a href="#" className="flex items-center justify-center space-x-2 text-[10px] uppercase tracking-widest hover:text-salon-gold transition-colors">
            <Instagram className="w-4 h-4" />
            <span>Follow on Instagram</span>
          </a>
        </div>
      </div>

      <AnimatePresence>
        {selectedImg && (
          <motion.div 
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-[100] bg-salon-ink/95 flex items-center justify-center p-6"
            onClick={() => setSelectedImg(null)}
          >
            <button className="absolute top-8 right-8 text-white">
              <X className="w-8 h-8" />
            </button>
            <motion.img 
              initial={{ scale: 0.9, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              src={selectedImg} 
              className="max-w-full max-h-[80vh] object-contain shadow-2xl"
              referrerPolicy="no-referrer"
            />
          </motion.div>
        )}
      </AnimatePresence>
    </section>
  );
};

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

const Booking = () => {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    service: '',
    date: '',
    time: ''
  });
  const [status, setStatus] = useState<'idle' | 'success'>('idle');

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (Object.values(formData).every(val => val !== '')) {
      setStatus('success');
      setTimeout(() => setStatus('idle'), 5000);
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
            <div className="grid grid-cols-1 md:grid-cols-2 gap-10">
              <div className="space-y-2">
                <label className="text-[10px] uppercase tracking-widest opacity-40">Name</label>
                <input 
                  type="text" 
                  required
                  className="w-full border-b border-salon-ink/10 py-2 focus:border-salon-gold outline-none transition-colors bg-transparent"
                  onChange={(e) => setFormData({...formData, name: e.target.value})}
                />
              </div>
              <div className="space-y-2">
                <label className="text-[10px] uppercase tracking-widest opacity-40">Email</label>
                <input 
                  type="email" 
                  required
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
                  className="w-full border-b border-salon-ink/10 py-2 focus:border-salon-gold outline-none transition-colors bg-transparent"
                  onChange={(e) => setFormData({...formData, phone: e.target.value})}
                />
              </div>
              <div className="space-y-2">
                <label className="text-[10px] uppercase tracking-widest opacity-40">Service</label>
                <select 
                  required
                  className="w-full border-b border-salon-ink/10 py-2 focus:border-salon-gold outline-none transition-colors bg-transparent"
                  onChange={(e) => setFormData({...formData, service: e.target.value})}
                >
                  <option value="">Select</option>
                  <option value="Cut">Haircut</option>
                  <option value="Color">Color</option>
                  <option value="Perm">Perm</option>
                  <option value="Treatment">Treatment</option>
                  <option value="Head Spa">Head Spa</option>
                  <option value="Extensions">Extensions</option>
                  <option value="SMP">SMP</option>
                </select>
              </div>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-10">
              <div className="space-y-2">
                <label className="text-[10px] uppercase tracking-widest opacity-40">Date</label>
                <input 
                  type="date" 
                  required
                  className="w-full border-b border-salon-ink/10 py-2 focus:border-salon-gold outline-none transition-colors bg-transparent"
                  onChange={(e) => setFormData({...formData, date: e.target.value})}
                />
              </div>
              <div className="space-y-2">
                <label className="text-[10px] uppercase tracking-widest opacity-40">Time</label>
                <input 
                  type="time" 
                  required
                  className="w-full border-b border-salon-ink/10 py-2 focus:border-salon-gold outline-none transition-colors bg-transparent"
                  onChange={(e) => setFormData({...formData, time: e.target.value})}
                />
              </div>
            </div>
            <button type="submit" className="w-full bg-salon-ink text-white py-4 text-[10px] uppercase tracking-[0.4em] hover:bg-salon-gold transition-all duration-500">
              Send Request
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
                3420 K Ave, Ste 214<br />
                Plano TX 75074<br />
                <span className="text-[10px] italic opacity-60">Located to the left of the entrance of H-Mart in Plano</span>
              </p>
            </div>
            <div>
              <p className="text-[10px] uppercase tracking-widest text-salon-gold mb-4 flex items-center">
                <Clock className="w-3 h-3 mr-2" /> Hours
              </p>
              <div className="text-sm opacity-70 leading-loose">
                <p>Mon - Sat: 10:00 AM - 7:00 PM</p>
                <p>Sun & Holidays: Appointment only</p>
              </div>
            </div>
            <div>
              <p className="text-[10px] uppercase tracking-widest text-salon-gold mb-4 flex items-center">
                <Phone className="w-3 h-3 mr-2" /> Contact
              </p>
              <div className="text-sm opacity-70 leading-relaxed">
                <p>Call : (972) 423 - 4212</p>
                <p>Text : (214) 934 - 3659</p>
                <p>Email : service@pikapikahairsalon.com</p>
              </div>
            </div>
          </div>
        </div>

        <div className="h-[500px] grayscale opacity-80 hover:grayscale-0 hover:opacity-100 transition-all duration-1000">
          <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3343.868725848529!2d-96.699444!3d33.046111!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x864c19396e2978f7%3A0x3ca58a57e193660d!2s3420%20K%20Ave%20%23214%2C%20Plano%2C%20TX%2075074!5e0!3m2!1sen!2sus!4v1710423340000!5m2!1sen!2sus" 
            width="100%" 
            height="100%" 
            style={{ border: 0 }} 
            allowFullScreen 
            loading="lazy" 
            referrerPolicy="no-referrer-when-downgrade"
          ></iframe>
        </div>
      </div>
    </div>
  </section>
);

const Footer = () => (
  <footer className="bg-white py-20 px-8 border-t border-salon-ink/5">
    <div className="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-12">
      <a href="#" className="text-2xl font-serif tracking-[0.4em] uppercase flex flex-col items-center">
        <span className="text-salon-ink">PIKA PIKA</span>
        <span className="text-[8px] tracking-[0.5em] -mt-1 opacity-50">HAIR SALON</span>
      </a>
      
      <div className="flex space-x-12">
        <a href="#" className="hover:text-salon-gold transition-colors"><Instagram className="w-5 h-5" /></a>
        <a href="#" className="hover:text-salon-gold transition-colors"><Facebook className="w-5 h-5" /></a>
      </div>

      <p className="text-[10px] uppercase tracking-[0.2em] opacity-40">
        Copyright &copy; 2024, pika pika hair salon, inc.
      </p>
    </div>
  </footer>
);

export default function App() {
  return (
    <div className="min-h-screen selection:bg-salon-gold/20">
      <Navbar />
      <Hero />
      <Story />
      <SignatureServices />
      <ServicesMenu />
      <Gallery />
      <Testimonials />
      <Booking />
      <Contact />
      <Footer />
    </div>
  );
}
