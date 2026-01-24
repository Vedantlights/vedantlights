import React from 'react';
import { Link } from 'react-router-dom';
import { FaFacebookF, FaTwitter, FaYoutube, FaLinkedinIn } from 'react-icons/fa';
import './Footer.css';

// Import images from assets folder
import whiteLogo from '../../assets/logo/whitelogo.png';

const Footer = () => {
  const socialLinks = [
    { icon: FaFacebookF, url: 'https://www.facebook.com/vedantlightsindiaprivatelimited/', color: 'var(--color-facebook)' },
    { icon: FaTwitter, url: 'https://x.com/IndiaLight60091', color: 'var(--color-twitter)' },
    { icon: FaYoutube, url: 'https://www.youtube.com/@vedantlights', color: 'var(--color-youtube)' },
    { icon: FaLinkedinIn, url: 'https://www.linkedin.com/in/sudhakar-poul/', color: 'var(--color-linkedin)' },
  ];

  return (
    <footer className="footer-main">
      <div className="container">
        <div className="footer-content">
          <div className="footer-logo">
            <Link to="/">
              <img 
                src={whiteLogo} 
                alt="Vedant Lights Logo"
                onError={(e) => {
                  e.target.src = 'https://via.placeholder.com/150x50?text=Vedant+Lights';
                }}
              />
            </Link>
          </div>
          
          <div className="footer-contact">
            <div className="footer-contact-item">
              <h6 className="footer-contact-heading">Contact no</h6>
              <a href="tel:+919860638920">9860638920 / 9890770189</a>
            </div>
          </div>
          
          <div className="footer-contact">
            <div className="footer-contact-item">
              <h6 className="footer-contact-heading">Email ID</h6>
              <a href="mailto:sudhakarpoul@vedantlights.com">
                sudhakarpoul@vedantlights.com<br />
                shital@vedantlights.com
              </a>
            </div>
          </div>
          
          <div className="footer-contact">
            <div className="footer-contact-item">
              <h6 className="footer-contact-heading">Address</h6>
              <a 
                href="https://maps.app.goo.gl/c7QFdqq2peZAKRpr5" 
                target="_blank" 
                rel="noopener noreferrer"
              >
                Office No. 21 & 22, 3rd Floor, Aston Plaza, Ambegaon Bk., Pune - 411046.
              </a>
            </div>
          </div>
        </div>
        
        <div className="footer-bottom">
          <div className="footer-social">
            {socialLinks.map((social, index) => {
              const Icon = social.icon;
              return (
                <a
                  key={index}
                  href={social.url}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="footer-social-link"
                  style={{ '--social-color': social.color }}
                  aria-label={`Visit our ${social.url.includes('facebook') ? 'Facebook' : social.url.includes('twitter') ? 'Twitter' : social.url.includes('youtube') ? 'YouTube' : 'LinkedIn'} page`}
                >
                  <Icon />
                </a>
              );
            })}
          </div>
          <div className="footer-copyright">
            <p>Copyright 2025. All Rights Reserved.</p>
          </div>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
