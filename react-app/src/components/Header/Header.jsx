import React, { useState, useEffect } from 'react';
import { Link, useMatch } from 'react-router-dom';
import { FaBars, FaTimes, FaWhatsapp } from 'react-icons/fa';
import { motion, AnimatePresence } from 'framer-motion';
import Button from '../Button/Button';
import './Header.css';

// Import images from assets folder
import logo from '../../assets/logo/logo.png';
import companyProfile from '../../assets/company_profile.pdf';
import lightsProduct from '../../assets/lights_product.pdf';
import streetLightPoles from '../../assets/street_light_poles.pdf';
import directorCard from '../../assets/director_business_card.jpeg';
import directorCard1 from '../../assets/director_business_card1.jpeg';

const Header = ({ brands = [] }) => {
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [isSticky, setIsSticky] = useState(false);
  const [isBrochureOpen, setIsBrochureOpen] = useState(false);
  const matchHome = useMatch({ path: '/', end: true });
  const matchAbout = useMatch('/aboutus');
  const matchContact = useMatch('/contactus');

  useEffect(() => {
    const handleScroll = () => {
      setIsSticky(window.scrollY > 100);
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const toggleMenu = () => setIsMenuOpen(!isMenuOpen);
  const closeMenu = () => setIsMenuOpen(false);

  const brochureItems = [
    { name: 'Company Profile', file: companyProfile },
    { name: 'Brochure - Lights Product', file: lightsProduct },
    { name: 'Brochure - Street Light Poles', file: streetLightPoles },
    { name: 'Director Business Card', file: directorCard },
    { name: 'Director Business Card', file: directorCard1 },
  ];

  return (
    <>
      <header className={`header-main ${isSticky ? 'header-sticky' : ''}`}>
        <div className="container">
          <div className="header-wrapper">
            <Link to="/" className="header-logo-area" onClick={closeMenu}>
              <img 
                src={logo} 
                alt="Vedant Lights Logo"
                onError={(e) => {
                  e.target.src = 'https://via.placeholder.com/150x50?text=Vedant+Lights';
                }}
              />
            </Link>

            <nav className={`header-nav ${isMenuOpen ? 'header-nav-open' : ''}`}>
              <ul className="header-nav-list">
                <li>
                  <Link 
                    to="/" 
                    className={matchHome ? 'header-active' : ''}
                    onClick={closeMenu}
                  >
                    HOME
                  </Link>
                </li>
                <li>
                  <Link 
                    to="/aboutus" 
                    className={matchAbout ? 'header-active' : ''}
                    onClick={closeMenu}
                  >
                    ABOUT US
                  </Link>
                </li>
                <li className="header-has-dropdown">
                  <span className="header-nav-link">BRAND</span>
                  <ul className="header-submenu">
                    {brands.map((brand, index) => (
                      <li key={brand.brand_id || index}>
                        <Link
                          to={`/brandDetails/${brand.brand_id}/${brand.brand_name}`}
                          onClick={closeMenu}
                        >
                          {brand.brand_name}
                        </Link>
                      </li>
                    ))}
                  </ul>
                </li>
                <li>
                  <Link 
                    to="/contactus" 
                    className={matchContact ? 'header-active' : ''}
                    onClick={closeMenu}
                  >
                    CONTACT
                  </Link>
                </li>
              </ul>
            </nav>

            <div className="header-actions">
              <button 
                className="header-menu-btn"
                onClick={toggleMenu}
                aria-label="Toggle menu"
              >
                {isMenuOpen ? <FaTimes /> : <FaBars />}
              </button>
              <Button
                href="https://api.whatsapp.com/send/?phone=%2B917709298685&text&type=phone_number&app_absent=0"
                variant="primary"
                icon={<FaWhatsapp />}
                iconPosition="left"
              >
                Contact Us
              </Button>
              <Button
                variant="secondary"
                onClick={() => setIsBrochureOpen(true)}
              >
                Download Brochure
              </Button>
            </div>
          </div>
        </div>
      </header>

      {/* Brochure Popup */}
      <AnimatePresence>
        {isBrochureOpen && (
          <motion.div
            className="header-brochure-overlay"
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            onClick={() => setIsBrochureOpen(false)}
          >
            <motion.div
              className="header-brochure-popup"
              initial={{ scale: 0.9, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.9, opacity: 0 }}
              onClick={(e) => e.stopPropagation()}
            >
              <h3>Download Brochures</h3>
              <div className="header-brochure-list">
                {brochureItems.map((item, index) => (
                  <div key={index} className="header-brochure-item">
                    <h6>{item.name}</h6>
                    <a
                      href={item.file}
                      download
                      className="header-download-btn"
                    >
                      Download
                    </a>
                  </div>
                ))}
              </div>
              <Button onClick={() => setIsBrochureOpen(false)}>Close</Button>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>

      {/* Mobile Sidebar */}
      <AnimatePresence>
        {isMenuOpen && (
          <motion.div
            className="header-mobile-sidebar"
            initial={{ x: '100%' }}
            animate={{ x: 0 }}
            exit={{ x: '100%' }}
            transition={{ type: 'tween', duration: 0.3 }}
          >
            <div className="header-sidebar-content">
              <button className="header-close-sidebar" onClick={closeMenu}>
                <FaTimes />
              </button>
              <nav className="header-mobile-nav">
                <Link to="/" onClick={closeMenu}>HOME</Link>
                <Link to="/aboutus" onClick={closeMenu}>ABOUT US</Link>
                <div className="header-mobile-brand-dropdown">
                  <span>BRAND</span>
                  <ul>
                    {brands.map((brand, index) => (
                      <li key={brand.brand_id || index}>
                        <Link
                          to={`/brandDetails/${brand.brand_id}/${brand.brand_name}`}
                          onClick={closeMenu}
                        >
                          {brand.brand_name}
                        </Link>
                      </li>
                    ))}
                  </ul>
                </div>
                <Link to="/contactus" onClick={closeMenu}>CONTACT</Link>
              </nav>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </>
  );
};

export default Header;
