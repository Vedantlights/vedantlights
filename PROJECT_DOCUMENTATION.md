# Vedant Lights - Project Documentation

## Overview

Vedant Lights is a web-based application built on CodeIgniter 4 framework for managing and displaying lighting products. The application serves as both a public-facing website showcasing lighting products and an administrative panel for managing brands, categories, and products.

---

## Functionality

### Public-Facing Features

#### Home Page
The home page serves as the main landing page displaying:
- Company branding and logo
- Hero banner with company introduction
- Service highlights including Trusted Brands, Leadership, Commitment to Quality, and Client Satisfaction
- Product range showcase featuring Street Lights, Flood Lights, Highbay Lights, and Flameproof Lights
- Company statistics and achievements
- Customer testimonials and feedback
- Global presence information
- Call-to-action sections

#### Brand Details
Users can browse products by brand. The system displays:
- List of all available brands in a dropdown navigation menu
- Brand-specific product categories
- Products filtered by selected brand and category
- Dynamic URL routing with brand ID and brand name parameters

#### Category Details
Products are organized by categories within each brand:
- Category listing based on selected brand
- Category-wise product filtering
- Multi-level navigation from brand to category to products

#### Product Details
Individual product pages display:
- Complete product information
- Product images
- Technical specifications
- Product descriptions
- Related brand and category information

#### Contact Us
Contact functionality includes:
- Contact form with name, email, subject, and message fields
- Email sending capability using CodeIgniter's email service
- AJAX support for form submission
- Success and error message handling
- Email notifications sent to company email address

#### About Us
Company information page providing:
- Company background and history
- Leadership information
- Company values and mission

### Administrative Features

#### Login System
- Session-based authentication
- Admin dashboard access control
- Secure login verification

#### Brand Management
Administrators can:
- Add new brands to the system
- Edit existing brand information
- View list of all brands
- Check for duplicate brand names
- Soft delete functionality (is_delete flag)

#### Category Management
Administrators can:
- Add categories associated with brands
- Edit category details
- View categories filtered by brand
- Manage category-brand relationships

#### Product Management
Administrators can:
- Add new products with images
- Edit product details including name, description, technical specifications
- Upload product images to server
- Associate products with brands and categories
- View comprehensive product listings with joins to brand and category tables
- Delete products from the system
- Validate product uniqueness within brand and category combinations

---

## Files

### Directory Structure

#### Application Core Files
- **app/Config/**: Contains all configuration files including database settings, routes, application settings, email configuration, and security settings
- **app/Controllers/**: Contains all controller classes that handle HTTP requests and business logic
- **app/Models/**: Contains data models that interact with the database
- **app/Views/**: Contains view templates organized by controller/feature
- **app/Database/**: Contains database migration and seed files
- **app/Helpers/**: Contains helper functions
- **app/Libraries/**: Contains custom library classes
- **app/Filters/**: Contains request filters for authentication and authorization

#### Public Assets
- **web_assets/**: Contains all frontend assets including CSS, JavaScript, images, and other static files
- **admin_asset/**: Contains administrative panel assets including CSS, JavaScript, and vendor libraries
- **uploads/**: Directory for storing uploaded files, particularly product images

#### System Files
- **system/**: CodeIgniter 4 framework core files (should not be modified)
- **writable/**: Directory for logs, cache, and other writable files
- **tests/**: Unit and integration test files

### Key Controller Files

#### Home Controller
Handles all public-facing pages including:
- Home page display
- Brand details page
- Category details page
- Product details page
- Contact us page
- About us page
- Email sending functionality for contact form

#### Brand Controller
Manages brand-related operations:
- Brand listing
- Adding new brands
- Editing existing brands
- Updating brand information
- Session-based access control

#### Product Controller
Manages product operations:
- Product listing with brand and category joins
- Adding new products with image upload
- Editing product details
- Deleting products
- Category selection based on brand (AJAX functionality)

#### Category Controller
Handles category management:
- Category listing
- Adding categories
- Editing categories
- Updating category information

#### Login Controller
Manages authentication:
- Login page display
- Session management
- Dashboard access

### Key Model Files

#### HomeModel
Provides data access methods for:
- Retrieving brand details
- Fetching category details by brand
- Getting products by brand and category
- Retrieving individual product details

#### BrandModel
Handles brand data operations:
- Inserting new brands
- Checking for duplicate brand names
- Retrieving brand lists
- Fetching individual brand details
- Updating brand information

#### ProductModel
Manages product data:
- Retrieving brand and category information
- Checking product existence
- Inserting new products
- Updating product information
- Deleting products
- Complex queries with joins for product listings

#### CategoryModel
Handles category data:
- Category retrieval by brand
- Category insertion and updates

### Key View Files

#### Public Views (app/Views/Home/)
- **webheader.php**: Main header template with navigation, logo, and menu structure
- **webfooter.php**: Footer template with closing HTML tags and scripts
- **index.php**: Home page template
- **brandDetails.php**: Brand-specific product listing page
- **productDetails.php**: Individual product detail page
- **contactus.php**: Contact form page
- **aboutus.php**: About us page template

#### Admin Views
- **login.php**: Admin login page
- **dashboard.php**: Admin dashboard
- **sidebar.php**: Admin sidebar navigation
- **header.php**: Admin panel header
- **footer.php**: Admin panel footer
- Brand, Category, and Product management views in respective directories

### Configuration Files

#### Routes Configuration (app/Config/Routes.php)
Defines all application routes including:
- Public routes for home, contact, about pages
- Brand and product detail routes with parameters
- Admin routes for dashboard and management functions
- POST routes for form submissions

#### Database Configuration (app/Config/Database.php)
Contains database connection settings:
- Database host, username, password
- Database name
- Connection driver (MySQLi)
- Character set and collation settings
- Test database configuration

#### Application Configuration (app/Config/App.php)
Defines application-wide settings:
- Base URL configuration
- Index page settings
- Locale and timezone settings
- Character encoding
- Security settings

#### Paths Configuration (app/Config/Paths.php)
Defines directory paths for:
- System directory
- Application directory
- Writable directory
- Tests directory
- View directory

---

## Logo

### Logo Location and Usage

#### Logo Files
The application uses multiple logo files stored in the **web_assets/images/logo/** directory:
- **logo.png**: Main company logo used in the website header
- **logo-01.png**: Alternative logo variant
- **logo-02.png**: Another logo variant
- **whitelogo.png**: White version of the logo for dark backgrounds

#### Logo Implementation

##### Public Website Header
The main logo is displayed in the website header on all public pages. It is implemented as a clickable link that redirects to the home page. The logo path is dynamically generated using CodeIgniter's base_url() helper function to ensure proper URL resolution regardless of the server configuration.

##### Logo Path Structure
The logo is referenced using the following path structure:
- Base URL + web_assets/images/logo/logo.png
- This ensures the logo loads correctly from any page in the application

##### Favicon
A separate favicon file is used for browser tab display:
- Located at: web_assets/images/fav.png
- Referenced in the HTML head section of all pages

##### Admin Panel Logo
The administrative panel uses a different logo/branding:
- Located at: admin_asset/images/brand/icon_black.svg
- Used in the page loader and admin interface

#### Logo Display Context
- **Header Navigation**: Logo appears in the top-left corner of the main navigation bar
- **Responsive Design**: Logo adapts to different screen sizes
- **Mobile Menu**: Logo is visible in mobile navigation
- **Homepage Link**: Logo serves as a home page link for better user experience

---

## Database

### Database Structure

#### Database Name
The application uses a MySQL database named **vedantlights_db**

#### Database Connection
- **Host**: localhost
- **Username**: vedantlights_db
- **Password**: Configured in database configuration file
- **Driver**: MySQLi
- **Character Set**: utf8
- **Collation**: utf8_general_ci
- **Port**: 3306

### Database Tables

#### brand_details Table
Stores brand information:
- **brand_id**: Primary key, auto-increment
- **brand_name**: Brand name (unique identifier)
- **is_delete**: Soft delete flag (0 = active, 1 = deleted)
- Used for organizing products by manufacturer/brand

#### category_details Table
Stores product categories:
- **cat_id**: Primary key, auto-increment
- **brandId**: Foreign key linking to brand_details table
- **category_name**: Name of the category
- Establishes relationship between brands and their product categories

#### product_details Table
Stores individual product information:
- **pro_id**: Primary key, auto-increment
- **brand_id**: Foreign key linking to brand_details table
- **catId**: Foreign key linking to category_details table
- **pro_name**: Product name
- **pro_desc**: Product description
- **pro_tech**: Technical specifications
- **pro_img**: Product image filename
- Contains the core product data displayed on the website

### Database Relationships

#### Hierarchical Structure
The database follows a three-level hierarchy:
1. **Brand Level**: Top-level organization (e.g., Crompton, Philips, Osram)
2. **Category Level**: Mid-level organization within each brand
3. **Product Level**: Individual products within each category

#### Foreign Key Relationships
- Products are linked to categories via catId
- Products are linked to brands via brand_id
- Categories are linked to brands via brandId
- This structure allows for efficient querying and filtering

#### Data Integrity
- Soft delete mechanism using is_delete flag prevents data loss
- Foreign key relationships ensure referential integrity
- Unique constraints prevent duplicate brand names

### Database Operations

#### Read Operations
- Fetching all active brands (is_delete = 0)
- Retrieving categories by brand ID
- Getting products filtered by brand and category
- Complex joins for product listings with brand and category information

#### Write Operations
- Inserting new brands with validation
- Adding categories associated with brands
- Creating products with image uploads
- Updating existing records

#### Delete Operations
- Soft delete for brands (setting is_delete flag)
- Hard delete for products (permanent removal)
- Cascade considerations for related data

---

## Path

### Directory Paths

#### Root Directory Structure
The application follows CodeIgniter 4 directory conventions with the following main paths:

#### Application Paths
- **Application Directory**: app/
  - Contains all application-specific code
  - Controllers, Models, Views, Config files
  - Path: Defined in app/Config/Paths.php as relative to project root

#### System Paths
- **System Directory**: system/
  - CodeIgniter 4 framework core files
  - Should not be modified directly
  - Path: Defined relative to project root

#### Public Assets Paths
- **Web Assets**: web_assets/
  - CSS files: web_assets/css/
  - JavaScript files: web_assets/js/
  - Images: web_assets/images/
  - Logo files: web_assets/images/logo/
  - Service images: web_assets/images/service/
  - Banner images: web_assets/images/banner/

#### Admin Assets Paths
- **Admin Assets**: admin_asset/
  - CSS: admin_asset/css/
  - JavaScript: admin_asset/js/
  - Vendor libraries: admin_asset/vendor/
  - Admin images: admin_asset/images/

#### Upload Paths
- **Upload Directory**: uploads/
  - Product images: uploads/Product/
  - Files are stored with original filenames
  - Path: ROOTPATH . 'uploads/Product'

#### Writable Paths
- **Writable Directory**: writable/
  - Logs: writable/logs/
  - Cache: writable/cache/
  - Debug bar data: writable/debugbar/
  - Session data storage

### URL Paths

#### Base URL Configuration
- **Base URL**: Configured in app/Config/App.php
- Production URL: https://www.vedantlights.com/
- Used for generating absolute URLs throughout the application

#### Route Paths
Routes are defined in app/Config/Routes.php:
- **Home**: / (root path)
- **Contact**: /contactus
- **About**: /aboutus
- **Brand Details**: /brandDetails/{brandId}/{brandName}
- **Product Details**: /productDetails/{productId}
- **Category Details**: /categoryDetails/{brandId}/{brandName}/{categoryId}
- **Admin**: /admin
- **Dashboard**: /dashboard

#### Asset Paths
Assets are referenced using base_url() helper:
- CSS: base_url() . 'web_assets/css/'
- JavaScript: base_url() . 'web_assets/js/'
- Images: base_url() . 'web_assets/images/'
- Logo: base_url() . 'web_assets/images/logo/logo.png'

### File System Paths

#### Absolute Paths
- **ROOTPATH**: CodeIgniter constant pointing to project root
- Used for file operations like image uploads
- Example: ROOTPATH . 'uploads/Product'

#### Relative Paths
- View includes use relative paths from Views directory
- Example: 'Home/webheader' for including header template

#### Path Helpers
- **base_url()**: Generates base URL for assets and links
- **site_url()**: Generates full URL including index.php if needed
- **ROOTPATH**: Constant for file system operations

---

## Request

### Request Handling Flow

#### HTTP Request Processing
The application follows CodeIgniter 4's request-response cycle:

1. **Request Reception**: Web server receives HTTP request
2. **Routing**: Routes.php determines which controller and method to call
3. **Controller Execution**: Appropriate controller method processes the request
4. **Model Interaction**: Controllers interact with models for data operations
5. **View Rendering**: Views are loaded and rendered with data
6. **Response**: HTML response sent back to client

### Request Types

#### GET Requests
Used for retrieving and displaying data:
- Home page display
- Brand listing pages
- Product detail pages
- Category pages
- About and Contact page displays
- Admin dashboard and listing pages

#### POST Requests
Used for form submissions and data modifications:
- Contact form submission
- Brand addition and updates
- Category addition and updates
- Product addition and updates
- Login authentication
- File uploads (product images)

#### AJAX Requests
Used for dynamic content loading:
- Category selection based on brand (in admin panel)
- Contact form submission with AJAX support
- Dynamic content updates without page refresh

### Request Service

#### Request Object
CodeIgniter's Request service is used throughout the application:
- **service('request')**: Gets the request service instance
- **getPost()**: Retrieves POST data
- **getFile()**: Retrieves uploaded files
- **isAJAX()**: Checks if request is AJAX

#### Request Data Handling
- POST data is retrieved using getPost() method
- Data validation is performed before processing
- Sanitization occurs to prevent security issues
- File uploads are handled separately using getFile()

### Route Parameters

#### Dynamic Route Parameters
Routes support dynamic parameters:
- Brand ID and name in brand details routes
- Product ID in product detail routes
- Category ID in category routes
- Base64 encoded IDs for admin edit operations

#### Parameter Extraction
- Parameters are extracted from URL segments
- Passed to controller methods as arguments
- Used for database queries and data retrieval

### Session Management

#### Session Usage
Sessions are used for:
- Admin authentication status
- Flash messages (success/error notifications)
- User state management

#### Session Service
- **\Config\Services::session()**: Gets session service
- **has()**: Checks if session variable exists
- **setFlashdata()**: Sets temporary session data for one request
- **getFlashdata()**: Retrieves flash data

### Request Validation

#### Input Validation
- Required field checks before processing
- Duplicate entry validation (brand names, products)
- File upload validation (image types, sizes)
- Email format validation for contact forms

#### Error Handling
- Validation errors redirect with flash messages
- AJAX requests return JSON error responses
- Logging of errors for debugging
- User-friendly error messages displayed

### File Upload Requests

#### Upload Handling
- Files are received via POST requests
- Product images uploaded to uploads/Product directory
- Original filenames preserved
- File validation before saving
- Old images replaced when updating products

#### Upload Path
- Files moved to ROOTPATH . 'uploads/Product'
- Filenames stored in database
- Images referenced via uploads directory in views

### Email Request Handling

#### Contact Form Processing
- POST request with form data
- Data extraction and validation
- HTML email composition
- Email service configuration
- Success/error response handling
- AJAX and standard form submission support

### Security Considerations

#### Request Security
- Session-based authentication for admin areas
- Input sanitization and validation
- CSRF protection (CodeIgniter built-in)
- SQL injection prevention through query builder
- XSS protection through output escaping

#### Access Control
- Admin routes protected by session checks
- Unauthorized access redirects to login
- Public routes accessible without authentication

---

## Additional Information

### Framework
- **Framework**: CodeIgniter 4
- **PHP Version**: 7.4 or higher required
- **Database**: MySQL
- **Architecture**: MVC (Model-View-Controller)

### Key Features
- Responsive design for mobile and desktop
- Multi-brand product catalog
- Hierarchical product organization
- Image upload and management
- Email functionality
- Admin panel for content management
- SEO-friendly URLs
- Session-based authentication

### Technology Stack
- **Backend**: PHP, CodeIgniter 4
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript, jQuery
- **Libraries**: Bootstrap, Swiper, Font Awesome, GSAP
- **Email**: CodeIgniter Email Library

---

*This documentation provides a comprehensive overview of the Vedant Lights application structure, functionality, and implementation details.*
