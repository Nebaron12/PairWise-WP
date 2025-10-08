# PairWise Battler - Installation Guide

## 🚀 Quick Install (5 Minutes)

### What You Need
- ✅ WordPress website (5.0 or higher)
- ✅ Elementor plugin installed (free version is fine)
- ✅ FTP access or File Manager in cPanel

---

## Step 1: Upload Plugin Files

### Option A: Via FTP (Recommended)

1. **Create the plugin folder:**
   - Connect to your site via FTP
   - Navigate to: `/wp-content/plugins/`
   - Create new folder: `pairwise-battler`

2. **Upload files:**
   - Upload `pairwise-battler.php` to this folder
   - Upload `widget-class.php` to this folder

3. **Final structure should be:**
   ```
   /wp-content/plugins/pairwise-battler/
   ├── pairwise-battler.php
   └── widget-class.php
   ```

### Option B: Via cPanel File Manager

1. Log into cPanel
2. Open "File Manager"
3. Navigate to `public_html/wp-content/plugins/`
4. Click "Create Folder" → Name it `pairwise-battler`
5. Open the folder
6. Click "Upload" → Select both PHP files
7. Wait for upload to complete

---

## Step 2: Activate Plugin

1. Log into your WordPress admin panel
2. Go to **Plugins → Installed Plugins**
3. Find "PairWise Battler" in the list
4. Click **"Activate"**
5. ✅ You should see "PairWise Battler" in the left sidebar menu

**If you see an error:**
- Make sure Elementor is installed and activated first
- Check PHP version is 7.0 or higher (ask your host)

---

## Step 3: Verify Installation

### Check Database Tables

1. Go to phpMyAdmin (or your database manager)
2. Select your WordPress database
3. Look for two new tables:
   - `wp_pairwise_results`
   - `wp_pairwise_summary`

✅ **If you see these tables, installation was successful!**

### Check Admin Menu

1. Look in WordPress admin sidebar
2. Find "PairWise Battler" menu item (with images icon)
3. Click it to open the dashboard
4. You should see "Overall Results" and "Per-User Results" tabs

---

## Step 4: Create Your First Test

### 4.1: Create a New Page

1. Go to **Pages → Add New**
2. Give it a title (e.g., "Camera Comparison Test")
3. Click **"Edit with Elementor"**

### 4.2: Add the Widget

1. In Elementor left panel, click the search box
2. Type: **"PairWise Battler"**
3. **Drag** the widget onto your page
4. You should see a placeholder widget

### 4.3: Add Your Images

1. **Click on the widget** to select it
2. In left panel, click **"Images"** tab
3. Click **"Add Item"**
4. Click **"Choose Image"** → Upload your first image
5. Enter an **"Image Title"** (e.g., "Camera A")
6. Click **"Add Item"** again for second image
7. Upload and name your second image
8. Add more images if needed (minimum 2 required)

### 4.4: Customize Text

1. Click **"Content"** tab in left panel
2. Change **"Heading Text"** if desired
3. Change **"Completion Text"** if desired
4. Change **"Reset Button Text"** if desired

### 4.5: Adjust Settings (Optional)

1. Click **"Settings"** tab
2. Toggle **"Shuffle Images"** ON if you want random order
3. Toggle **"Show Progress Bar"** ON to show progress
4. Enter a **"Session Name"** (e.g., "October-2025-Test")

### 4.6: Style It (Optional)

1. Click **"Style"** tab
2. Change **"Primary Color"** for buttons and progress bar
3. Adjust **"Card Background"** color
4. Customize **"Text Color"**
5. Change **"Border Radius"** for rounded corners

### 4.7: Publish

1. Click **"Publish"** button (bottom left)
2. Click **"View Page"** to see it live

---

## Step 5: Test It

1. **Open your published page**
2. **Click on one of the images** to choose it
3. **Next pair appears** automatically
4. **Continue** until all pairs are compared
5. **See results screen** with winning image
6. Data is **automatically saved**!

---

## Step 6: View Results

1. Go to **WordPress Admin**
2. Click **"PairWise Battler"** in sidebar
3. See **"Overall Results"** tab:
   - Aggregate data from all tests
   - Ranked by wins and click rate
   - Export all data button
4. Click **"Per-User Results"** tab:
   - Individual test sessions
   - Filter by session name
   - Export individual session

---

## ✅ Installation Complete!

You now have:
- ✅ Plugin installed and activated
- ✅ Database tables created
- ✅ Widget available in Elementor
- ✅ First test created
- ✅ Results visible in admin

---

## 🎯 Next Steps

### Add More Tests
- Create additional pages with different image sets
- Use different session names to track separately
- A/B test designs, products, layouts, etc.

### Share Your Test
- Copy the page URL
- Share on social media
- Email to team/customers
- Embed in existing pages

### Analyze Results
- Check results daily in admin dashboard
- Export CSV for detailed analysis
- Compare sessions over time
- Make data-driven decisions

---

## 🔧 Troubleshooting

### "Widget not found in Elementor"
**Fix:**
1. Make sure Elementor is activated
2. Go to Plugins → Deactivate PairWise Battler
3. Reactivate PairWise Battler
4. Refresh Elementor editor

### "Images not saving"
**Fix:**
1. Test your site's REST API: Visit `yourdomain.com/wp-json/`
2. If you see JSON data, API is working
3. Check browser console (F12) for errors
4. Try disabling security plugins temporarily

### "No results showing in admin"
**Fix:**
1. Make sure you completed at least one full test
2. Check database tables exist in phpMyAdmin
3. Look for `wp_pairwise_results` and `wp_pairwise_summary`
4. If missing, deactivate and reactivate plugin

### "Permission error"
**Fix:**
1. Log in as Administrator
2. Check user role has "manage_options" capability
3. Try different user account

---

## 📞 Need Help?

### Resources
- 📖 Full documentation: `PAIRWISE_BATTLER_README.md`
- ❓ Common issues: See troubleshooting section above
- 💬 Community support: [Your support URL]

### Before Asking for Help
1. Check Elementor is activated
2. Check PHP version (7.0+ required)
3. Check WordPress version (5.0+ required)
4. Try deactivating/reactivating plugin
5. Check browser console for JavaScript errors
6. Test with default WordPress theme

---

## 🎉 You're Ready!

Start collecting preference data and making better decisions!

**Happy Testing!** 🚀
