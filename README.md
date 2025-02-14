# Rise GA4 Auto Events

A WordPress plugin that automatically configures and sends essential user interaction events to your Google Analytics 4 property. Track important user engagement metrics without any manual configuration.

## Features

- **Automatic Event Tracking:**
  - Phone Call Clicks
  - Form Submissions
  - Email Link Clicks

- **Zero Configuration Required:** Simply install, activate, and enter your GA4 property ID
- **Lightweight:** No impact on site performance
- **Privacy Compliant:** Respects user privacy settings and GDPR requirements

## Installation

1. Download the plugin ZIP file
2. Go to WordPress Admin > Plugins > Add New
3. Click "Upload Plugin" and select the downloaded ZIP file
4. Click "Install Now"
5. After installation, click "Activate"

## Configuration

1. Navigate to WordPress Admin > Settings > Rise GA4 Auto Events
2. Enter your Google Analytics 4 Property ID
3. Save changes
4. Events will start tracking automatically

![image](https://github.com/user-attachments/assets/05438e20-2a7c-4b8e-87e5-45935f45e3d8)



## Events Tracked

### Phone Call Tracking
- Automatically tracks when visitors click on phone numbers
- Event Name: `phone_call`
- Parameters:
  - `phone_number`: The clicked phone number
  - `page_title`: Title of the page where the click occurred

### Form Submissions
- Tracks all form submissions across your site
- Event Name: `form_submission`
- Parameters:
  - `form_id`: Identifier of the submitted form
  - `form_name`: Name/title of the form
  - `page_url`: URL where the form was submitted

### Email Click Tracking
- Monitors clicks on email links (mailto:)
- Event Name: `email_click`
- Parameters:
  - `email_address`: The clicked email address
  - `page_title`: Title of the page where the click occurred

![image](https://github.com/user-attachments/assets/c356a4d5-3d8b-4396-bf81-7f464c7e8f58)


## Support

For support queries or feature requests, please create an issue in our GitHub repository.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

---

Made with ❤️ by [Rise](https://riseseo.com.au)
