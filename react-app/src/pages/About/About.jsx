import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Link } from 'react-router-dom';
import { FaArrowRight, FaCheck, FaClipboardCheck, FaDollarSign, FaCogs, FaHeadset } from 'react-icons/fa';
import Button from '../../components/Button/Button';
import './About.css';

// Import images from assets folder
import nightlightImage from '../../assets/about/nightlight.png';
import solutionImage from '../../assets/solution/01.png';

const About = () => {
  const [currentTestimonial, setCurrentTestimonial] = useState(0);
  const [isMobile, setIsMobile] = useState(window.innerWidth <= 768);

  const features = [
    {
      title: 'Wide Product Range',
      description: 'Explore our extensive catalog of industrial products to meet all your wholesale needs.',
      icon: FaClipboardCheck
    },
    {
      title: 'Competitive Pricing',
      description: 'Benefit from competitive prices, ensuring cost-effectiveness for your business.',
      icon: FaDollarSign
    },
    {
      title: 'Fast Shipping',
      description: 'Enjoy timely deliveries to keep your operations running smoothly.',
      icon: FaCogs
    },
    {
      title: 'Customer Support',
      description: 'Our team is ready to assist you with any inquiries or assistance required.',
      icon: FaHeadset
    }
  ];

  const testimonials = [
    {
      name: 'Vinod Shinde',
      role: 'Director, GD Lights',
      text: 'Vedant Lights truly stands out! Their commitment to excellence is evident in every product. Eco-friendly, efficient, and visually stunning – a perfect combination.'
    },
    {
      name: 'Rahul Jadhav',
      role: 'Director, Solotronics',
      text: 'Vedant Lights transformed our office space! The quality of their lighting solutions exceeded our expectations. Eco-friendly and efficient – exactly what we needed!'
    },
    {
      name: 'Vijay Jain',
      role: 'Director, Volta',
      text: 'Exceptional service and top-notch products! Vedant Lights not only brightened our home but also provided expert guidance. Highly recommended!'
    }
  ];

  useEffect(() => {
    const handleResize = () => {
      setIsMobile(window.innerWidth <= 768);
    };
    window.addEventListener('resize', handleResize);
    return () => window.removeEventListener('resize', handleResize);
  }, []);

  // Auto-play carousel
  useEffect(() => {
    const interval = setInterval(() => {
      setCurrentTestimonial((prev) => {
        const maxIndex = isMobile
          ? testimonials.length - 1
          : Math.max(0, testimonials.length - 2);
        return prev >= maxIndex ? 0 : prev + 1;
      });
    }, 4000); // Change slide every 4 seconds

    return () => clearInterval(interval);
  }, [isMobile, testimonials.length]);

  return (
    <div className="about-page">
      {/* Breadcrumb */}
      <section className="about-breadcrumb-section">
        <div className="container">
          <motion.div
            className="about-breadcrumb-content"
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
          >
            <span className="about-breadcrumb-bg-text">About Us</span>
            <h1 className="about-breadcrumb-title">About Us</h1>
            <div className="about-breadcrumb-links">
              <Link to="/">HOME /</Link>
              <span className="about-active">ABOUT US</span>
            </div>
          </motion.div>
        </div>
      </section>

      {/* About Content */}
      <section className="about-content-section section-gap">
        <div className="container">
          <div className="about-wrapper">
            <motion.div
              className="about-image"
              initial={{ opacity: 0, x: -30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6 }}
            >
              <img 
                src={nightlightImage} 
                alt="Vedant Lights"
                onError={(e) => {
                  e.target.src = 'https://via.placeholder.com/600x400?text=Vedant+Lights';
                }}
              />
            </motion.div>

            <motion.div
              className="about-text"
              initial={{ opacity: 0, x: 30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6 }}
            >
              <div className="about-section-header-left">
                <p className="about-section-pre">
                  <span>Company Overview</span>
                </p>
                <h2 className="about-section-title">
                  Illuminate Your Industry with<br />
                  Premium Industrial Lighting Solutions
                </h2>
              </div>
              
              <div className="about-description">
                <p>
                  Welcome to Vedant Lights India Pvt. Ltd. Founded in 2021
                </p>
                <p>
                  At Vedant Lights India Pvt Ltd, we illuminate the world with a commitment to excellence in providing cutting-edge electrical solutions. With a foundation built on integrity, transparency, and accountability, we embrace a forward-looking approach to cater to diverse industrial lighting needs.
                </p>
                <p>
                  We are proud to uphold our commitment to excellence in providing a diverse range of industrial lighting solutions. Our core values of integrity, transparency, and accountability drive our forward-looking approach. With an innovative team, we set the highest standards of quality, embracing technology to exceed client expectations. Thank you for being part of our story; we look forward to creating a brighter future together.
                </p>
              </div>
            </motion.div>
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section className="about-features-section section-gap">
        <div className="container">
          <motion.div
            className="about-section-header"
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
          >
            <p className="about-section-pre">
              <span>Quality Product</span> Guarantees
            </p>
            <h2 className="about-section-title">Why Choose Us</h2>
          </motion.div>
          
          <div className="about-features-grid">
            {features.map((feature, index) => {
              const IconComponent = feature.icon;
              return (
                <motion.div
                  key={index}
                  className="about-feature-card"
                  initial={{ opacity: 0, y: 30 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  viewport={{ once: true }}
                  transition={{ duration: 0.5, delay: index * 0.1 }}
                  whileHover={{ y: -5 }}
                >
                  <div className="about-feature-icon-wrapper">
                    <div className="about-feature-icon-circle">
                      <IconComponent className="about-feature-icon" />
                    </div>
                  </div>
                  <div className="about-feature-content">
                    <h5 className="about-feature-title">{feature.title}</h5>
                    <p className="about-feature-description">{feature.description}</p>
                  </div>
                  <div className="about-feature-number">
                    <span>{String(index + 1).padStart(2, '0')}</span>
                  </div>
                </motion.div>
              );
            })}
          </div>
        </div>
      </section>

      {/* Testimonials Section */}
      <section className="about-testimonials-section section-gap about-bg-feedback">
        <div className="container">
          <div className="about-testimonials-wrapper">
            <motion.div
              className="about-testimonials-image"
              initial={{ opacity: 0, x: -30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6 }}
            >
              <img 
                src={solutionImage} 
                alt="Solutions"
                onError={(e) => {
                  e.target.src = 'https://via.placeholder.com/500x400?text=Solutions';
                }}
              />
            </motion.div>
            
            <motion.div
              className="about-testimonials-content"
              initial={{ opacity: 0, x: 30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6 }}
            >
              <div className="about-section-header-left">
                <p className="about-section-pre">
                  <span>Only Quality</span> Solution
                </p>
            <h2 className="about-section-title">
              Amazing Feedback Say<br />
              About Products
            </h2>
          </div>

          <div className="about-testimonials-carousel-wrapper">
            <div className="about-testimonials-carousel">
              <div
                className="about-testimonials-track"
                style={{
                  transform: `translateX(-${
                    currentTestimonial * (isMobile ? 100 : 50)
                  }%)`,
                }}
              >
                {testimonials.map((testimonial, index) => {
                  const isVisible = isMobile
                    ? index === currentTestimonial
                    : index === currentTestimonial ||
                      index === currentTestimonial + 1;
                  return (
                    <div
                      key={index}
                      className={`about-testimonial-item ${
                        isVisible ? 'active' : ''
                      }`}
                    >
                      <div className="about-testimonial-header">
                        <div className="about-testimonial-info">
                          <h5>{testimonial.name}</h5>
                          <span>{testimonial.role}</span>
                        </div>
                      </div>
                      <div className="about-testimonial-body">
                        <p>"{testimonial.text}"</p>
                      </div>
                    </div>
                  );
                })}
              </div>
            </div>
          </div>

          <div className="about-testimonials-dots">
            {Array.from({
              length: isMobile ? testimonials.length : Math.max(1, testimonials.length - 1),
            }).map((_, index) => (
              <button
                key={index}
                className={`about-testimonial-dot ${
                  index === currentTestimonial ? 'active' : ''
                }`}
                onClick={() => setCurrentTestimonial(index)}
                aria-label={`Go to testimonial ${index + 1}`}
              />
            ))}
          </div>
            </motion.div>
          </div>
        </div>
      </section>
    </div>
  );
};

export default About;
