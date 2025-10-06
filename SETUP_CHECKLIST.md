# Camera Battle - Quick Setup Checklist

## ✅ Installation Checklist

### Step 1: Install WordPress Plugin
- [ ] Upload `camera-battle-saver.php` to `/wp-content/plugins/camera-battle-saver/`
- [ ] Go to WordPress Admin → Plugins
- [ ] Find "Camera Battle Results Saver"
- [ ] Click "Activate"
- [ ] Verify database tables are created (check phpMyAdmin if needed)

### Step 2: Verify Plugin Installation
- [ ] Look for "Camera Battle" in WordPress admin sidebar (with 📸 icon)
- [ ] Click on it to open the admin dashboard
- [ ] You should see three tabs (even if no data yet)

### Step 3: Add HTML Widget to Elementor
- [ ] Edit your page in Elementor
- [ ] Add an HTML widget where you want the test
- [ ] Copy ALL contents of `Main.html`
- [ ] Paste into the HTML widget
- [ ] Update the image URLs in the widget to your actual images
- [ ] Customize the session name in `data-session="your-session-name"`
- [ ] Save and publish the page

### Step 4: Customize Your Test
- [ ] Update image URLs to your actual images
- [ ] Change image titles in `data-title="Your Title"`
- [ ] Set a meaningful session name (e.g., `data-session="october-2025-test"`)
- [ ] Adjust styling if needed (CSS section)
- [ ] Test on desktop and mobile

### Step 5: Test the Integration
- [ ] Visit your page with the widget
- [ ] Complete the entire comparison test
- [ ] Click "Download CSV" (now saves to database)
- [ ] You should see "Results saved successfully to database!" message
- [ ] Go to WordPress Admin → Camera Battle
- [ ] Verify your session appears in the dropdown
- [ ] Check that data shows in all three tabs

### Step 6: Share with Users
- [ ] Test on different browsers (Chrome, Firefox, Safari)
- [ ] Test on mobile devices
- [ ] Verify the test works for multiple users
- [ ] Add privacy notice if needed
- [ ] Share the page link with your audience

## 🔧 Configuration Options

### HTML Widget Attributes

```html
<div class="cb-widget" id="camera-battle-1"
     data-session="your-session-name"
     data-shuffle="1"
     data-progress="1"
     data-webhook="">
```

| Attribute | Options | Description |
|-----------|---------|-------------|
| `id` | Any unique ID | Identifier for this widget instance |
| `data-session` | Any text | Session name (groups results together) |
| `data-shuffle` | `1` or `0` | Randomize comparison order |
| `data-progress` | `1` or `0` | Show progress bar |
| `data-webhook` | URL | Optional: Send data to external service |

### Recommended Settings

**For A/B Testing:**
```html
data-session="product-test-november-2025"
data-shuffle="1"
data-progress="1"
```

**For Sequential Testing:**
```html
data-session="ordered-test-v1"
data-shuffle="0"
data-progress="1"
```

## 📊 Understanding Results

### Metrics Explained

**Clicks** (formerly "wins")
- Every time an image is selected
- Tracked throughout all comparisons
- Shows overall preference

**Complete Wins** (new)
- Only when an image wins the final available comparison
- More definitive measure of preference
- Best indicator of true winner

**Appearances**
- How many times image was shown
- Should be similar across all images
- Used to calculate click rate

**Click Rate**
- Percentage: (Clicks / Appearances) × 100
- Shows consistency of preference
- Color-coded in admin dashboard

## 🎯 Best Practices

### Session Naming
✅ Good:
- `camera-test-oct-2025`
- `product-comparison-v1`
- `design-ab-test-homepage`

❌ Avoid:
- `test` (too generic)
- `abc123` (not descriptive)
- Spaces or special characters

### Image Selection
- Use high-quality images
- Keep file sizes reasonable (<500KB)
- Use similar dimensions for fair comparison
- Host on reliable CDN or WordPress media library

### Sample Size
- **Minimum**: 30 users for reliable results
- **Good**: 50-100 users
- **Excellent**: 100+ users
- More users = more statistical confidence

### Test Duration
- Run for at least 1-2 weeks
- Consider day-of-week variations
- Account for different time zones
- Don't make decisions too quickly

## 🐛 Troubleshooting

### Common Issues

**Issue: "Could not save to database"**
- Check plugin is activated
- Verify REST API is enabled
- Check browser console for errors
- Fallback: CSV will download automatically

**Issue: No data in admin dashboard**
- Verify session name matches exactly
- Check users completed the full test
- Look in Raw Data tab for partial data
- Check WordPress error logs

**Issue: Images not loading**
- Verify image URLs are correct
- Check images are publicly accessible
- Test URLs directly in browser
- Check for HTTPS/HTTP mixed content

**Issue: Multiple users have same ID**
- Each browser generates unique ID
- Private browsing creates new ID
- Clearing localStorage resets ID
- This is normal behavior

## 📈 Analyzing Results

### Quick Analysis Steps

1. **Go to Overall Results tab**
   - Look at Complete Wins column
   - Check Click Rate percentages
   - Note any outliers

2. **Review Per User Results**
   - Check for consistency
   - Identify any unusual patterns
   - Verify diverse opinions

3. **Export CSV for Deep Analysis**
   - Open in Excel/Google Sheets
   - Create pivot tables
   - Calculate statistics
   - Generate visualizations

### Statistical Considerations

**Confidence Levels:**
- 30-50 users: Basic trends
- 50-100 users: Good confidence
- 100+ users: High confidence
- 500+ users: Very high confidence

**Margin of Error:**
- Small samples = larger margin
- Calculate using online calculators
- Consider statistical significance
- Don't over-interpret small differences

## 🔒 Security Notes

- REST API endpoint is public (necessary for frontend)
- All inputs are sanitized
- SQL injection protection via prepared statements
- Admin dashboard requires WordPress admin privileges
- Consider rate limiting for high-traffic sites

## 📞 Support

### Getting Help

1. Check the README.md
2. Review ADMIN_GUIDE.md
3. Check WordPress error logs
4. Review browser console
5. Test with different browsers

### Reporting Issues

When reporting problems, include:
- WordPress version
- PHP version
- Browser and version
- Error messages (if any)
- Steps to reproduce

## 🚀 Next Steps

After successful setup:
- [ ] Run a pilot test with 5-10 users
- [ ] Review results in admin dashboard
- [ ] Adjust if needed
- [ ] Launch to full audience
- [ ] Monitor results regularly
- [ ] Export data for analysis
- [ ] Make data-driven decisions

## 📝 Notes

- Data is stored permanently until manually deleted
- Consider periodic backups of database
- Archive old sessions if no longer needed
- Document your testing methodology
- Keep track of changes between sessions

---

**Remember:** The goal is to gather meaningful data to make informed decisions. Take your time, collect enough responses, and analyze thoughtfully!
