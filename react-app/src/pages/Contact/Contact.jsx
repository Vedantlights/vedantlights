import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { Link } from 'react-router-dom';
import Button from '../../components/Button/Button';
import './Contact.css';
import { backendPath } from '../../lib/backend';

const Contact = () => {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    subject: '',
    message: ''
  });
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitStatus, setSubmitStatus] = useState(null);

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);
    setSubmitStatus(null);

    try {
      const body = new FormData();
      body.append('name', formData.name);
      body.append('email', formData.email);
      body.append('subject', formData.subject);
      body.append('message', formData.message);

      const res = await fetch(backendPath('/sendmail'), {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body
      });
      const payload = await res.json().catch(() => null);

      if (res.ok && payload?.status === 'success') {
        setSubmitStatus('success');
        setFormData({ name: '', email: '', subject: '', message: '' });
        setTimeout(() => setSubmitStatus(null), 5000);
      } else {
        console.error('Contact form error:', payload);
        setSubmitStatus('error');
        setTimeout(() => setSubmitStatus(null), 5000);
      }
    } catch (err) {
      console.error('Error submitting contact form:', err);
      setSubmitStatus('error');
      setTimeout(() => setSubmitStatus(null), 5000);
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="contact-page">
      {/* Breadcrumb */}
      <section className="contact-breadcrumb-section">
        <div className="container">
          <motion.div
            className="contact-breadcrumb-content"
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
          >
            <span className="contact-breadcrumb-bg-text">Contact Us</span>
            <h1 className="contact-breadcrumb-title">Contact Us</h1>
            <div className="contact-breadcrumb-links">
              <Link to="/">HOME /</Link>
              <span className="contact-active">CONTACT</span>
            </div>
          </motion.div>
        </div>
      </section>

      {/* Contact Form Section */}
      <section className="contact-form-section section-gap">
        <div className="container">
          <div className="contact-wrapper">
            <motion.div
              className="contact-form-area"
              initial={{ opacity: 0, x: -30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6 }}
            >
              <div className="contact-form-header">
                <p className="contact-section-pre">
                  <span>Feel Free</span> To Contact Us
                </p>
                <h2 className="contact-section-title">Get in Touch</h2>
              </div>

              {submitStatus === 'success' && (
                <motion.div
                  className="contact-form-message contact-form-message-success"
                  initial={{ opacity: 0, y: -10 }}
                  animate={{ opacity: 1, y: 0 }}
                >
                  Thank you! Your message has been sent successfully.
                </motion.div>
              )}

              {submitStatus === 'error' && (
                <motion.div
                  className="contact-form-message contact-form-message-error"
                  initial={{ opacity: 0, y: -10 }}
                  animate={{ opacity: 1, y: 0 }}
                >
                  Sorry, there was an error sending your message. Please try again later.
                </motion.div>
              )}

              <form className="contact-form" onSubmit={handleSubmit}>
                <div className="contact-form-row">
                  <div className="contact-form-group">
                    <input
                      type="text"
                      id="name"
                      name="name"
                      placeholder="Your Name"
                      value={formData.name}
                      onChange={handleChange}
                      required
                      className="contact-form-input"
                    />
                  </div>
                  <div className="contact-form-group">
                    <input
                      type="email"
                      id="email"
                      name="email"
                      placeholder="Email Address"
                      value={formData.email}
                      onChange={handleChange}
                      required
                      className="contact-form-input"
                    />
                  </div>
                </div>

                <div className="contact-form-group">
                  <input
                    type="text"
                    id="subject"
                    name="subject"
                    placeholder="Subject"
                    value={formData.subject}
                    onChange={handleChange}
                    className="contact-form-input"
                  />
                </div>

                <div className="contact-form-group">
                  <textarea
                    id="message"
                    name="message"
                    placeholder="Type Your Message"
                    value={formData.message}
                    onChange={handleChange}
                    required
                    rows="6"
                    className="contact-form-textarea"
                  />
                </div>

                <Button
                  type="submit"
                  variant="primary"
                  size="large"
                  disabled={isSubmitting}
                >
                  {isSubmitting ? 'Sending...' : 'Send Message'}
                </Button>
              </form>
            </motion.div>

            <motion.div
              className="contact-map-area"
              initial={{ opacity: 0, x: 30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6 }}
            >
              <div className="contact-map-wrapper">
                <iframe
                  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1892.3746566519344!2d73.83467593857581!3d18.44968789565754!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bc2953224a9ac11%3A0x4aae6f87fb362c4b!2sVEDANT%20LIGHTS%20INDIA%20PRIVATE%20LIMITED!5e0!3m2!1sen!2sin!4v1687853706887!5m2!1sen!2sin"
                  width="100%"
                  height="100%"
                  style={{ border: 0, borderRadius: 'var(--radius-lg)' }}
                  allowFullScreen=""
                  loading="lazy"
                  referrerPolicy="no-referrer-when-downgrade"
                  title="Vedant Lights Location"
                />
              </div>
            </motion.div>
          </div>
        </div>
      </section>
    </div>
  );
};

export default Contact;
