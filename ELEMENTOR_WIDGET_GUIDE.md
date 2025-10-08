# Camera Battle - Elementor Widget Installation Guide

## 📦 What's Included

This package includes:
1. **camera-battle-elementor-widget.php** - Main plugin file
2. **widgets/camera-battle-widget.php** - Widget class
3. **camera-battle-saver.php** - Database plugin (previously installed)

## 🚀 Installation Steps

### Step 1: Install the Elementor Widget Plugin

1. Create a folder named `camera-battle-elementor-widget` in your WordPress plugins directory
2. Copy these files into the folder:
   ```
   /wp-content/plugins/camera-battle-elementor-widget/
   ├── camera-battle-elementor-widget.php
   └── widgets/
       └── camera-battle-widget.php
   ```

3. Go to **WordPress Admin → Plugins**
4. Find "Camera Battle Elementor Widget"
5. Click **Activate**

### Step 2: Verify Installation

- Make sure **Elementor** is installed and activated
- Make sure **Camera Battle Results Saver** plugin is installed and activated
- Check for any error messages

## 🎨 Using the Widget in Elementor

### Adding the Widget

1. Edit a page with **Elementor**
2. Search for "Camera Battle" in the widget panel
3. Drag and drop the widget onto your page
4. Configure the settings (see below)

### Widget Settings

#### Content Tab

**General Settings:**
- **Heading Text**: Main question shown to users (default: "Which photo looks better?")
- **Completion Text**: Message shown when test is complete
- **Reset Button Text**: Text for the restart button

**Images Section:**
- Click **"Add Item"** to add more images
- For each image:
  - Upload/select image from media library
  - Enter a descriptive title (e.g., "Camera A", "iPhone 15", "Sony A7")
- Reorder images by dragging
- Remove images with the delete icon
- **Minimum 2 images required**

**Settings:**
- **Randomize Order**: Shuffle comparison order for each user
- **Show Progress Bar**: Display progress indicator
- **Base Session Name**: Used for grouping results (e.g., "phone-test", "camera-comparison")
- **Webhook URL**: Optional external service integration

#### Style Tab

Customize the appearance:
- **Heading Color**: Change heading text color
- **Heading Typography**: Font, size, weight, etc.
- **Card Border Radius**: Round corners of image cards (0-50px)
- **Button Background**: Reset button background color
- **Button Text Color**: Reset button text color

## 📋 Configuration Examples

### Example 1: Phone Camera Test
```
Heading: "Which phone takes better photos?"
Images:
  - iPhone 15 Pro
  - Samsung Galaxy S24
  - Google Pixel 8
  - OnePlus 12
Base Session: "phone-camera-test"
Randomize: Yes
Progress Bar: Yes
```

### Example 2: Product Photography
```
Heading: "Which product photo do you prefer?"
Images:
  - Natural Lighting
  - Studio Lighting
  - Golden Hour
Base Session: "product-photos"
Randomize: No (fixed order)
Progress Bar: Yes
```

### Example 3: A/B Testing
```
Heading: "Which design looks better?"
Images:
  - Design Option A
  - Design Option B
Base Session: "homepage-design"
Randomize: Yes
Progress Bar: No
```

## 🎯 Best Practices

### Image Selection
- Use high-quality images
- Keep file sizes reasonable (< 500KB recommended)
- Use consistent dimensions across all images
- Use descriptive titles

### Testing
- Add at least 2 images (no maximum)
- More images = more comparisons (factorial)
  - 2 images = 1 comparison
  - 3 images = 3 comparisons
  - 4 images = 6 comparisons
  - 5 images = 10 comparisons

### Session Naming
- Use descriptive names (e.g., "spring-2025-test")
- Avoid special characters
- Keep it short and memorable
- Session name gets auto-appended with timestamp

## 📊 Viewing Results

### Access Admin Dashboard
1. Go to **WordPress Admin**
2. Click **Camera Battle** in the sidebar
3. View results in two tabs:
   - **Overall Results**: Aggregated data from all tests
   - **Per User Results**: Individual test results by session

### Export Data
- **Overall Tab**: Export all data from all sessions
- **Per User Tab**: Export specific session data

## 🔧 Customization

### Styling with CSS
You can add custom CSS in Elementor or your theme:

```css
/* Change card hover effect */
.cb-card:hover {
    transform: scale(1.05);
}

/* Customize progress bar color */
.cb-progress-bar {
    background: linear-gradient(90deg, #your-color-1, #your-color-2);
}

/* Style the results table */
.cb-complete table {
    font-family: 'Your Font', sans-serif;
}
```

### Advanced: Multiple Widgets
You can add multiple Camera Battle widgets on different pages:
- Each widget operates independently
- Use different session names to separate results
- Results are grouped by session in the admin panel

## 🐛 Troubleshooting

### Widget Not Appearing
- Verify Elementor is installed and activated
- Check that the plugin is activated
- Clear Elementor cache (Elementor → Tools → Regenerate CSS)
- Refresh the Elementor editor

### Images Not Loading
- Check image URLs are correct
- Verify images are publicly accessible
- Check for mixed content (HTTP/HTTPS) issues
- Try re-uploading images

### Results Not Saving
- Verify "Camera Battle Results Saver" plugin is activated
- Check WordPress REST API is enabled
- Review browser console for errors
- Verify database tables exist

### Widget Styling Issues
- Clear browser cache
- Clear WordPress cache
- Regenerate Elementor CSS
- Check for theme CSS conflicts

## 🔄 Updating

When updating the widget:
1. Deactivate the old plugin
2. Replace plugin files
3. Reactivate the plugin
4. Regenerate Elementor CSS
5. Test on a staging page first

## 📱 Mobile Responsiveness

The widget is fully responsive:
- Images stack vertically on mobile
- Cards maintain aspect ratio
- Progress bar adapts to screen size
- Results table is scrollable on small screens

## 🔐 Security

- All inputs are sanitized and escaped
- REST API uses WordPress nonces
- SQL queries use prepared statements
- Images are validated on upload

## 🆘 Support

For issues or questions:
1. Check WordPress error logs
2. Check browser console for JavaScript errors
3. Verify all plugins are up to date
4. Test with default WordPress theme
5. Disable other plugins to check for conflicts

## 📝 Notes

- Widget data is stored in localStorage until test completion
- Each browser session gets a unique ID
- Results are saved to database when test completes
- Reset button only appears after test completion
- Multiple users can complete test on same device

## 🎓 Tips for Best Results

1. **Clear Instructions**: Use descriptive heading text
2. **Quality Images**: High-resolution, consistent style
3. **Meaningful Titles**: Help users identify options
4. **Appropriate Quantity**: 3-5 images is ideal
5. **Test First**: Complete test yourself before launching
6. **Collect Enough Data**: Aim for 50-100+ responses
7. **Analyze Regularly**: Check results in admin dashboard

## 🚀 Next Steps

After installation:
1. ✅ Add widget to a test page
2. ✅ Configure with your images
3. ✅ Customize styling to match your brand
4. ✅ Preview and test functionality
5. ✅ Publish and share with your audience
6. ✅ Monitor results in admin dashboard

---

**Enjoy using Camera Battle Elementor Widget!** 📸✨
