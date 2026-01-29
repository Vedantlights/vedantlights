import React from 'react';
import { Link } from 'react-router-dom';
import './Button.css';

const Button = ({ 
  children, 
  variant = 'primary', 
  size = 'medium',
  href,
  to,
  onClick,
  className = '',
  type = 'button',
  disabled = false,
  icon,
  iconPosition = 'right',
  target,
  rel
}) => {
  const baseClasses = `button-btn button-btn-${variant} button-btn-${size} ${className}`;
  
  const content = (
    <>
      {icon && iconPosition === 'left' && <span className="button-btn-icon-left">{icon}</span>}
      <span>{children}</span>
      {icon && iconPosition === 'right' && <span className="button-btn-icon-right">{icon}</span>}
    </>
  );

  if (to) {
    return (
      <Link 
        to={to} 
        className={baseClasses}
        onClick={onClick}
        aria-label={children}
      >
        {content}
      </Link>
    );
  }

  if (href) {
    return (
      <a 
        href={href} 
        className={baseClasses}
        onClick={onClick}
        aria-label={children}
        target={target}
        rel={rel}
      >
        {content}
      </a>
    );
  }

  return (
    <button
      type={type}
      className={baseClasses}
      onClick={onClick}
      disabled={disabled}
      aria-label={children}
    >
      {content}
    </button>
  );
};

export default Button;
