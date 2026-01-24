import React, { useState, useEffect, useRef } from 'react';
import { motion, useInView } from 'framer-motion';
import { Link } from 'react-router-dom';
import { FaArrowRight, FaPhone, FaCheck, FaChevronLeft, FaChevronRight } from 'react-icons/fa';
import Button from '../../components/Button/Button';
import './Home.css';

// Import images from assets folder
import trustIcon from '../../assets/home/trust.png';
import medalIcon from '../../assets/home/medal.png';
import shieldIcon from '../../assets/home/shield.png';
import reviewIcon from '../../assets/home/review.png';
import streetLights from '../../assets/service/Street_Lights.png';
import floodLights from '../../assets/service/Flood_lights.png';
import highbayLights from '../../assets/service/Highbay_Lights.png';
import flameproofLights from '../../assets/service/Flalmeproof_Lights.png';
import ctaImage05 from '../../assets/cta/05.png';
import ctaImage06 from '../../assets/cta/06.png';
import exportImage from '../../assets/solution/export.png';
import teamImage21 from '../../assets/team/21.png';

// CountUp component for animated numbers
const CountUp = ({ end, suffix = '', duration = 2, className = '' }) => {
  const [count, setCount] = useState(0);
  const ref = useRef(null);
  const isInView = useInView(ref, { once: true, margin: '-100px' });

  useEffect(() => {
    if (!isInView) return;

    let startTime = null;
    const startValue = 0;
    const endValue = parseInt(end) || 0;

    const animate = (currentTime) => {
      if (!startTime) startTime = currentTime;
      const progress = Math.min((currentTime - startTime) / (duration * 1000), 1);
      
      // Easing function for smooth animation
      const easeOutQuart = 1 - Math.pow(1 - progress, 4);
      const currentCount = Math.floor(startValue + (endValue - startValue) * easeOutQuart);
      
      setCount(currentCount);

      if (progress < 1) {
        requestAnimationFrame(animate);
      } else {
        setCount(endValue);
      }
    };

    requestAnimationFrame(animate);
  }, [isInView, end, duration]);

  return (
    <h2 ref={ref} className={className}>
      {count.toLocaleString()}{suffix}
    </h2>
  );
};

const Home = () => {
  const [currentTestimonial, setCurrentTestimonial] = useState(0);
  const [isMobile, setIsMobile] = useState(window.innerWidth <= 768);

  useEffect(() => {
    const handleResize = () => {
      setIsMobile(window.innerWidth <= 768);
    };
    window.addEventListener('resize', handleResize);
    return () => window.removeEventListener('resize', handleResize);
  }, []);

  const services = [
    {
      icon: trustIcon,
      title: 'Trusted Brands',
      description: 'Proud partners of leading brands - Crompton, Philips, Osram, and more. Committed to quality'
    },
    {
      icon: medalIcon,
      title: 'Leadership',
      description: 'Meet our visionaries, Mr. Sudhakar Poul & Mrs. Shital Mastud, setting industry standards.'
    },
    {
      icon: shieldIcon,
      title: 'Commitment to Quality',
      description: 'Certified excellence: Our products meet and exceed industry standards with BIS, ISI, and more.'
    },
    {
      icon: reviewIcon,
      title: 'Client Satisfaction',
      description: 'Explore our successful projects: Efficient, reliable illumination. Client testimonials attest to our customer satisfaction commitment.'
    }
  ];

  const products = [
    {
      image: streetLights,
      title: 'Street Lights',
      specs: ['20-250 Watt', 'Cool & Warm White', 'Color Temp: 3000K-5700K']
    },
    {
      image: floodLights,
      title: 'Flood Lights',
      specs: ['20-1000 Watt', 'Cool & Warm White', 'Color Temp: 3000K-5700K']
    },
    {
      image: highbayLights,
      title: 'Highbay Lights',
      specs: ['80-250 Watt', 'Cool & Warm White', 'Color Temp: 3000K-5700K']
    },
    {
      image: flameproofLights,
      title: 'Flameproof Lights',
      specs: ['80-200 Watt', 'Cool & Warm White', 'Color Temp: 3000K-5700K']
    }
  ];

  const testimonials = [
    {
      text: 'Exceptional service and top-notch products! Vedant Lights not only brightened our home but also provided expert guidance. Highly recommended!',
      rating: 4.2
    },
    {
      text: 'Our outdoor space has never looked better! Vedant Lights delivered on their promise of quality and innovation. Delighted with the results!',
      rating: 4.9
    },
    {
      text: 'Vedant Lights truly stands out! Their commitment to excellence is evident in every product. Eco-friendly, efficient, and visually stunning – a perfect combination.',
      rating: 4.9
    },
    {
      text: 'Choosing Vedant Lights was a game-changer for our store. The innovative lighting solutions added a unique touch, attracting more customers. Thank you for the brilliance!',
      rating: 4.9
    }
  ];

  const expertise = [
    {
      icon: ctaImage06,
      title: 'Professional Industrial Light Experts',
      description: 'Vedant Lights: Your Professional Industrial Light Experts. With years of expertise, we specialize in providing cutting-edge lighting solutions tailored for industrial environments. Trust us to illuminate your workspace with precision and efficiency'
    },
    {
      icon: ctaImage06,
      title: '24/7 Customer Support',
      description: 'Vedant Lights: Your Illumination Partner with 24/7 Customer Support. We prioritize your needs around the clock, ensuring a seamless experience and addressing your inquiries promptly. Count on us for unwavering support in every lighting solution.'
    }
  ];

  const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
      opacity: 1,
      transition: {
        staggerChildren: 0.1
      }
    }
  };

  const itemVariants = {
    hidden: { opacity: 0, y: 20 },
    visible: {
      opacity: 1,
      y: 0,
      transition: { duration: 0.5 }
    }
  };

  return (
    <div className="home-page">
      {/* Hero Banner */}
      <section className="home-hero-banner">
        <div className="home-hero-background">
          <div className="container">
            <motion.div
              className="home-hero-content"
              initial={{ opacity: 0, y: 30 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8 }}
            >
              <h1 className="home-hero-title">
                Vedant Lights: Lighting Your Path to Excellence
              </h1>
              <p className="home-hero-description">
                Vedant Lights: Masterfully tailoring eco-friendly illumination for 5 years.<br />
                Illuminate spaces with our global quality commitment and cutting-edge technology.<br />
                Illuminate with absolute confidence—choose Vedant Lights.
              </p>
              <div className="home-hero-actions">
                <Button href="/aboutus" variant="primary" size="large">
                  More About Us
                </Button>
              </div>
            </motion.div>
          </div>
        </div>
      </section>

      {/* Services Section */}
      <section className="home-services-section section-gap">
        <div className="container">
          <motion.div
            className="home-section-header"
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
          >
            <h2 className="home-section-title">Why Choose Us ?</h2>
          </motion.div>
          
          <motion.div
            className="home-services-grid"
            variants={containerVariants}
            initial="hidden"
            whileInView="visible"
            viewport={{ once: true }}
          >
            {services.map((service, index) => (
              <motion.div
                key={index}
                className="home-service-card"
                variants={itemVariants}
                whileHover={{ y: -5 }}
                transition={{ duration: 0.3 }}
              >
                <div className="home-service-icon">
                  <img 
                    src={service.icon} 
                    alt={service.title}
                    onError={(e) => {
                      e.target.style.display = 'none';
                    }}
                  />
                </div>
                <h6 className="home-service-title">{service.title}</h6>
                <p className="home-service-description">{service.description}</p>
              </motion.div>
            ))}
          </motion.div>
        </div>
      </section>

      {/* Products Section */}
      <section className="home-products-section section-gap home-bg-dark">
        <div className="container">
          <motion.div
            className="home-section-header"
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
          >
            <h2 className="home-section-title">Our wide Range of Products...</h2>
          </motion.div>
          
          <motion.div
            className="home-products-grid"
            variants={containerVariants}
            initial="hidden"
            whileInView="visible"
            viewport={{ once: true }}
          >
            {products.map((product, index) => (
              <motion.div
                key={index}
                className="home-product-card"
                variants={itemVariants}
                whileHover={{ y: -8, scale: 1.02 }}
                transition={{ duration: 0.3 }}
              >
                <div className="home-product-image">
                  <img 
                    src={product.image} 
                    alt={product.title}
                    onError={(e) => {
                      e.target.src = 'https://via.placeholder.com/300x200?text=' + product.title;
                    }}
                  />
                </div>
                <div className="home-product-content">
                  <h5 className="home-product-title">{product.title}</h5>
                  <ul className="home-product-specs">
                    {product.specs.map((spec, i) => (
                      <li key={i}>{spec}</li>
                    ))}
                  </ul>
                </div>
              </motion.div>
            ))}
          </motion.div>
          
          <motion.div
            className="home-contact-info-box"
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6, delay: 0.2 }}
          >
            <p>
              Call us today: <a href="tel:+919860638920">+91 9860638920 / 9890770189</a> <span>or</span>
            </p>
            <p>
              Email us: <a href="mailto:sudhakarpoul@vedantlights.com">sudhakarpoul@vedantlights.com</a>
            </p>
          </motion.div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="home-cta-section section-gap">
        <div className="container">
          <div className="home-cta-wrapper">
            <motion.div
              className="home-cta-image"
              initial={{ opacity: 0, x: -30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6 }}
            >
              <img 
                src={ctaImage05} 
                alt="Vedant Lights"
                onError={(e) => {
                  e.target.style.display = 'none';
                }}
              />
            </motion.div>
            
            <motion.div
              className="home-cta-content"
              initial={{ opacity: 0, x: 30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6 }}
            >
              <h2 className="home-cta-title">
                At Vedant Lights India Pvt Ltd,<br />
                We are proud to uphold our commitment
              </h2>
              
              <div className="home-expertise-list">
                {expertise.map((item, index) => (
                  <motion.div
                    key={index}
                    className="home-expertise-item"
                    initial={{ opacity: 0, y: 20 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.5, delay: index * 0.1 }}
                  >
                    <div className="home-expertise-icon">
                      <img 
                        src={item.icon} 
                        alt={item.title}
                        onError={(e) => {
                          e.target.style.display = 'none';
                        }}
                      />
                    </div>
                    <div className="home-expertise-info">
                      <h5>{item.title}</h5>
                      <p>{item.description}</p>
                    </div>
                  </motion.div>
                ))}
              </div>
              
              <div className="home-cta-actions">
                <Button variant="primary" size="large" icon={<FaArrowRight />}>
                  Read More
                </Button>
                <div className="home-call-button">
                  <FaPhone />
                  <div className="home-call-info">
                    <span>Call Us 24/7</span>
                    <a href="tel:+919860638920">+91 9860638920</a>
                  </div>
                </div>
              </div>
            </motion.div>
          </div>
        </div>
      </section>

      {/* Stats Section */}
      <section className="home-stats-section section-gap home-bg-feedback">
        <div className="container">
          <div className="home-stats-wrapper">
            <motion.div
              className="home-stats-content"
              initial={{ opacity: 0, x: -30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6 }}
            >
              <div className="home-stat-card home-stat-card-large">
                <CountUp end={2000} suffix="+" className="home-stat-number" />
                <h5>Happy Customers</h5>
                <p>Customer Satisfaction is our Priority!</p>
              </div>
              <div className="home-stat-card home-stat-card-small">
                <CountUp end={100} suffix="+" className="home-stat-number" />
                <h5>Products Range</h5>
                <p>Great products with high quality</p>
              </div>
            </motion.div>
            
            <motion.div
              className="home-stats-info"
              initial={{ opacity: 0, x: 30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6 }}
            >
              <div className="home-stats-text">
                <p className="home-stats-pre">
                  <span>Our World</span> Wide Presence
                </p>
                <h3>List of Countries We Serve</h3>
                <p className="home-stats-description">
                  Sri Lanka, Qatar, Kuwait, Saudi Arabia, UAE, Oman, Australia, Nepal, Singapore, Tanzania, Canada, Bangladesh
                </p>
                <div className="home-rating-box">
                  <span className="home-rating-score">4.7/5</span>
                  <div className="home-rating-info">
                    <p>India Mart Rated</p>
                    <span>Rated by over 500 customers</span>
                  </div>
                </div>
              </div>
              <div className="home-stats-image">
                <img 
                  src={exportImage} 
                  alt="Global Presence"
                  onError={(e) => {
                    e.target.style.display = 'none';
                  }}
                />
              </div>
            </motion.div>
          </div>
        </div>
      </section>

      {/* Testimonials Section */}
      <section className="home-testimonials-section section-gap">
        <div className="container">
          <motion.div
            className="home-section-header"
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
          >
            <p className="home-section-pre">
              <span>Our</span> Clients Review
            </p>
            <h2 className="home-section-title">Customer Feedbacks</h2>
          </motion.div>
          
          <div className="home-testimonials-carousel-wrapper">
            <button 
              className="home-testimonial-nav-btn home-testimonial-nav-prev"
              onClick={() => setCurrentTestimonial((prev) => {
                const maxIndex = isMobile 
                  ? testimonials.length - 1 
                  : Math.max(0, testimonials.length - 2);
                return prev === 0 ? maxIndex : prev - 1;
              })}
              aria-label="Previous testimonial"
            >
              <FaChevronLeft />
            </button>
            
            <div className="home-testimonials-carousel">
              <div 
                className="home-testimonials-track"
                style={{ transform: `translateX(-${currentTestimonial * (isMobile ? 100 : 50)}%)` }}
              >
                {testimonials.map((testimonial, index) => {
                  const isVisible = isMobile 
                    ? index === currentTestimonial 
                    : index === currentTestimonial || index === currentTestimonial + 1;
                  return (
                    <div
                      key={index}
                      className={`home-testimonial-card ${isVisible ? 'active' : ''}`}
                    >
                      <div className="home-testimonial-avatar">
                        <img 
                          src={teamImage21} 
                          alt="Customer"
                          onError={(e) => {
                            e.target.src = 'https://via.placeholder.com/100';
                          }}
                        />
                      </div>
                      <div className="home-testimonial-content">
                        <p className="home-testimonial-text">{testimonial.text}</p>
                        <div className="home-testimonial-rating">
                          <div className="home-stars">
                            {[...Array(5)].map((_, i) => (
                              <FaCheck key={i} className="home-star-icon" />
                            ))}
                          </div>
                          <p>{testimonial.rating} Out of 5 Star</p>
                        </div>
                      </div>
                    </div>
                  );
                })}
              </div>
            </div>
            
            <button 
              className="home-testimonial-nav-btn home-testimonial-nav-next"
              onClick={() => setCurrentTestimonial((prev) => {
                const maxIndex = isMobile 
                  ? testimonials.length - 1 
                  : Math.max(0, testimonials.length - 2);
                return prev >= maxIndex ? 0 : prev + 1;
              })}
              aria-label="Next testimonial"
            >
              <FaChevronRight />
            </button>
          </div>
          
          <div className="home-testimonials-dots">
            {Array.from({ length: isMobile ? testimonials.length : Math.max(1, testimonials.length - 1) }).map((_, index) => (
              <button
                key={index}
                className={`home-testimonial-dot ${index === currentTestimonial ? 'active' : ''}`}
                onClick={() => setCurrentTestimonial(index)}
                aria-label={`Go to testimonial ${index + 1}`}
              />
            ))}
          </div>
        </div>
      </section>
    </div>
  );
};

export default Home;
