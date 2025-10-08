# PairWise Battler - WordPress Plugin

**Version:** 1.0.0  
**Requires:** WordPress 5.0+, Elementor 3.0+, PHP 7.0+

## Description

PairWise Battler is an interactive image comparison widget for Elementor that helps you make data-driven decisions through preference-based voting. Perfect for A/B testing, product selection, design choices, photography contests, and more.

Users compare images pairwise until a winner emerges. All results are automatically saved to your WordPress database with a comprehensive admin dashboard for analysis.

## Features

### 🎨 For Site Builders
- **Elementor Widget** - Drag and drop widget with visual controls
- **No Coding Required** - Configure everything through Elementor editor
- **Fully Responsive** - Works perfectly on desktop, tablet, and mobile
- **Customizable Styling** - Colors, typography, spacing all editable
- **Multiple Images** - Add 2 to unlimited images for comparison
- **Easy Setup** - Install, activate, and start using in minutes

### 📊 For Data Analysis
- **Automatic Saving** - Results saved to database on completion
- **Admin Dashboard** - View results in WordPress admin panel
- **Overall Results** - See aggregate data across all sessions
- **Per-User Results** - Filter and analyze individual test sessions
- **CSV Export** - Export single sessions or all data
- **Smart Ranking** - Ranks by complete wins, then click rate

### 🚀 For Users
- **Clean Interface** - Modern, intuitive design
- **Progress Tracking** - Shows how many comparisons remain
- **Session Management** - Unique session per user/device
- **Auto-Save** - Results automatically saved on completion
- **Results Display** - See winning image after all comparisons

## Installation

### Method 1: Upload Plugin File

1. Download `pairwise-battler.php` and `widget-class.php`
2. Create folder: `/wp-content/plugins/pairwise-battler/`
3. Upload both files to this folder
4. Go to WordPress Admin → Plugins
5. Activate "PairWise Battler"

### Method 2: Direct Upload

1. Go to WordPress Admin → Plugins → Add New
2. Click "Upload Plugin"
3. Upload the plugin ZIP file
4. Click "Install Now"
5. Click "Activate Plugin"

## Requirements

✅ WordPress 5.0 or higher  
✅ Elementor 3.0 or higher (free or pro)  
✅ PHP 7.0 or higher  
✅ MySQL database

## Quick Start Guide

### Step 1: Install Plugin
- Upload and activate the plugin
- Database tables will be created automatically
- Check for "PairWise Battler" menu in WordPress admin sidebar

### Step 2: Create a Page
- Edit any page with Elementor
- Search for "PairWise Battler" widget in Elementor panel
- Drag the widget to your page

### Step 3: Add Images
- Click on the widget to open settings
- Go to "Images" tab
- Click "Add Item" for each image you want to compare
- Upload images and add titles

### Step 4: Customize
- **Content Tab**: Edit heading, completion message, button text
- **Settings Tab**: Toggle shuffle, progress bar, set session name
- **Style Tab**: Customize colors, fonts, borders, spacing

### Step 5: Publish
- Click "Publish" or "Update"
- Test the comparison on your live page
- View results in WordPress Admin → PairWise Battler

## Widget Settings

### Content Tab

**Heading Text**
- Text shown above the comparison cards
- Default: "Which photo looks better?"

**Completion Text**
- Message shown when all comparisons are done
- Default: "All battles complete! Thanks for voting."

**Reset Button Text**
- Text on the restart button
- Default: "Restart"

### Images Tab

**Image List (Repeater)**
- Add unlimited images
- Each item has:
  - Image upload field
  - Title/name field
- Minimum 2 images required
- Drag to reorder images

### Settings Tab

**Shuffle Images**
- Randomize image order for each user
- Default: OFF

**Show Progress Bar**
- Display progress indicator
- Default: ON

**Session Name**
- Custom identifier for this test
- Default: "Default"
- Used for filtering results

**Webhook URL** (Optional)
- Send results to external service
- Leave blank to disable

### Style Tab

**Primary Color**
- Main accent color
- Used for progress bar, buttons

**Card Background**
- Background color of image cards
- Default: White

**Text Color**
- Color of all text elements
- Default: Dark gray

**Border Radius**
- Roundness of corners
- Range: 0-50px

**Typography**
- Heading font, size, weight
- Button font, size, weight
- Body text styling

## Admin Dashboard

Access the dashboard: **WordPress Admin → PairWise Battler**

### Overall Results Tab

Shows aggregate results across ALL sessions:
- Rank (determined by complete wins, then click rate)
- Image name
- Total complete wins
- Total clicks
- Click rate percentage
- Export All Data button (CSV)

**Winner is highlighted in green**

### Per-User Results Tab

Shows individual test sessions:
- Filter by specific session
- Columnar layout (images as columns, sessions as rows)
- Result numbering (Result 1, Result 2, etc.)
- Totals row at bottom
- Export This Session button (CSV)

Each cell shows:
- Complete wins
- Total clicks

## How It Works

### 1. User Experience
```
User opens page
    ↓
Widget loads with configured images
    ↓
User sees 2 images side-by-side
    ↓
User clicks preferred image
    ↓
Next pair appears
    ↓
Process repeats until all pairs compared
    ↓
Results screen shows winner
    ↓
Data auto-saved to database
```

### 2. Data Collection

**Tracking Metrics:**
- **Clicks**: Total times image was clicked
- **Complete Wins**: Times image won full comparison

**Session Info:**
- Unique ID format: YYYY-MM-DD-HH-MM-RandomID
- Stored in browser localStorage
- One session per user/device
- Can't vote twice from same device

### 3. Data Storage

**Two Database Tables:**

`wp_pairwise_results`
- Individual vote records
- Columns: id, session_id, image_name, clicks, complete_wins, created_at

`wp_pairwise_summary`
- Complete session summaries
- Columns: id, session_id, session_data (JSON), created_at

## Use Cases

### 1. Marketing A/B Testing
```
Scenario: Test two ad designs
Setup: Add both designs to widget
Result: See which gets more preference
Benefit: Data-driven ad selection
```

### 2. Product Selection
```
Scenario: Choose products for inventory
Setup: Add product photos
Result: See customer preferences
Benefit: Stock what sells
```

### 3. Design Decisions
```
Scenario: Team can't agree on logo
Setup: Add logo options
Result: Collective preference revealed
Benefit: Democratic decision making
```

### 4. Photography Contest
```
Scenario: Public voting for best photo
Setup: Add contest entries
Result: Track votes in real-time
Benefit: Transparent winner selection
```

### 5. Website Redesign
```
Scenario: Test homepage layouts
Setup: Add mockup screenshots
Result: User preference data
Benefit: Choose most appealing design
```

## API Endpoints

### Save Results
```
POST /wp-json/pairwise-battler/v1/save-results
```

**Request Body:**
```json
{
  "sessionId": "2025-10-08-14-30-abc123",
  "results": [
    {
      "name": "Image 1",
      "clicks": 5,
      "completeWins": 3
    }
  ]
}
```

**Response:**
```json
{
  "success": true
}
```

### Get Results (Admin Only)
```
GET /wp-json/pairwise-battler/v1/get-results?session_id=2025-10-08-14-30-abc123
```

**Response:**
```json
[
  {
    "id": 1,
    "session_id": "2025-10-08-14-30-abc123",
    "image_name": "Image 1",
    "clicks": 5,
    "complete_wins": 3,
    "created_at": "2025-10-08 14:35:22"
  }
]
```

## Customization

### Custom CSS

Add to Elementor widget's Advanced → Custom CSS:

```css
/* Larger image cards */
.pw-card {
    width: 400px;
}

/* Custom shadow */
.pw-card {
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

/* Custom hover effect */
.pw-card:hover {
    transform: scale(1.05);
    transition: transform 0.3s ease;
}

/* Result images larger */
.pw-result-img {
    width: 150px;
    height: 150px;
}
```

### Custom Colors via Theme

```css
:root {
    --pw-primary: #667eea;
    --pw-primary-dark: #5a67d8;
    --pw-card-bg: #ffffff;
    --pw-text: #2d3748;
}
```

### Webhook Integration

Enable webhook in widget settings to send results to external services:

```javascript
// Webhook payload sent:
{
  "sessionId": "2025-10-08-14-30-abc123",
  "results": [...]
}
```

## Troubleshooting

### Widget Not Appearing in Elementor

**Solution:**
1. Check Elementor is activated
2. Verify Elementor version is 3.0+
3. Deactivate and reactivate PairWise Battler
4. Clear Elementor cache: Tools → Regenerate CSS

### Images Not Saving

**Solution:**
1. Check WordPress REST API is working: Visit `/wp-json/`
2. Check browser console for errors (F12)
3. Verify database tables exist in phpMyAdmin
4. Check file permissions

### Admin Page Not Showing Results

**Solution:**
1. Complete at least one test first
2. Check you're logged in as admin
3. Clear browser cache
4. Check database tables have data

### Images Not Loading

**Solution:**
1. Re-upload images
2. Check image URLs in Elementor settings
3. Verify images are in Media Library
4. Check file permissions on uploads folder

## Performance

### Optimization Tips

**For Large Sites:**
- Use image optimization plugin (e.g., Smush, ShortPixel)
- Compress images before upload
- Use WebP format if possible
- Limit image count to 10-15 for best UX

**Database:**
- Plugin is lightweight
- Tables are indexed for fast queries
- Handles thousands of sessions easily
- Use CSV export for large datasets

## Security

### Built-in Protection
✅ Nonce verification  
✅ SQL injection prevention  
✅ XSS protection  
✅ CSRF protection  
✅ Capability checks  
✅ Input sanitization  
✅ Output escaping  

### Best Practices
- Keep WordPress updated
- Keep Elementor updated
- Use strong admin passwords
- Regular database backups

## Support

### Documentation
- Installation guide: See above
- Video tutorials: Coming soon
- FAQ: See Troubleshooting section

### Community
- GitHub: [Your repo URL]
- WordPress.org: [Plugin page]

## Changelog

### 1.0.0 - October 2025
- Initial release
- Elementor widget integration
- Database storage
- Admin dashboard
- CSV export functionality
- Overall and per-user results
- Session filtering
- Responsive design

## Credits

**Developed by:** [Your Name]  
**License:** GPL v2 or later  
**Icons:** Dashicons (bundled with WordPress)

## License

This plugin is licensed under the GPL v2 or later.

```
Copyright (C) 2025

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

## File Structure

```
pairwise-battler/
├── pairwise-battler.php       (Main plugin file)
└── widget-class.php           (Elementor widget class)
```

## What's Next?

✅ **You're all set!** Start creating comparison tests.

**Quick Links:**
- 📖 [Back to Installation](#installation)
- 🚀 [Quick Start Guide](#quick-start-guide)
- ⚙️ [Widget Settings](#widget-settings)
- 📊 [Admin Dashboard](#admin-dashboard)

---

**Made with ❤️ for better decision making**
