# 📁 FILE GUIDE - What to Upload vs What to Keep

## 🎯 UPLOAD THESE FILES (Required)

```
📁 /wp-content/plugins/pairwise-battler/
│
├── 📄 pairwise-battler.php        ✅ REQUIRED - Main plugin file
└── 📄 widget-class.php            ✅ REQUIRED - Widget class
```

**These 2 files are ALL you need!**

---

## 📚 DOCUMENTATION FILES (Optional - For Reference)

```
📄 PAIRWISE_BATTLER_README.md        📖 Complete documentation
📄 INSTALL_GUIDE.md                  📖 Installation instructions  
📄 QUICK_REFERENCE.md                📖 Quick cheat sheet
📄 SINGLE_PLUGIN_SUMMARY.md          📖 Project overview
📄 PACKAGING_GUIDE.md                📖 Distribution guide
📄 PROJECT_COMPLETION_SUMMARY.md     📖 What was done
```

**Keep these for reference - don't need to upload to WordPress**

---

## ❌ OLD FILES (Do Not Upload)

```
📄 camera-battle-elementor-widget.php     ❌ OLD - Replaced by pairwise-battler.php
📄 camera-battle-saver.php                ❌ OLD - Merged into pairwise-battler.php
📄 camera-battle-saver.php.zip            ❌ OLD - Archive file
📁 widgets/camera-battle-widget.php       ❌ OLD - Replaced by widget-class.php
📄 Main.html                              ❌ OLD - Standalone HTML version
📄 ADMIN_GUIDE.md                         ❌ OLD - Superseded by new docs
📄 ELEMENTOR_WIDGET_GUIDE.md              ❌ OLD - Superseded by new docs
📄 SETUP_CHECKLIST.md                     ❌ OLD - Superseded by new docs
📄 PACKAGE_STRUCTURE.md                   ❌ OLD - Superseded by new docs
📄 README.md                              ❌ OLD - For HTML version
```

**These are legacy files from the old "Camera Battle" version**

---

## 📊 Visual Comparison

### OLD System (Don't Use)
```
❌ Complex Structure:

camera-battle-saver.php
camera-battle-elementor-widget.php
widgets/
  └── camera-battle-widget.php

= 3 files in 2 locations
= 2 separate plugins
= Confusing setup
```

### NEW System (Use This!)
```
✅ Simple Structure:

pairwise-battler.php
widget-class.php

= 2 files in 1 location
= 1 single plugin
= Easy setup
```

---

## 🎯 Quick Decision Tree

### "What should I upload?"

```
Do you want a working WordPress plugin?
    ↓
   YES
    ↓
Upload ONLY:
  • pairwise-battler.php
  • widget-class.php
    ↓
  DONE!
```

### "What about the other files?"

```
Are they documentation (*.md files)?
    ↓
   YES → Keep for reference, don't upload
    ↓
   NO
    ↓
Do they have "camera-battle" in the name?
    ↓
   YES → OLD files, don't upload
    ↓
   NO → Check this guide
```

---

## 📦 For Distribution (ZIP File)

### Minimal Package
```
pairwise-battler.zip
└── pairwise-battler/
    ├── pairwise-battler.php
    └── widget-class.php
```

### With Documentation
```
pairwise-battler.zip
└── pairwise-battler/
    ├── pairwise-battler.php
    ├── widget-class.php
    ├── README.md (rename from PAIRWISE_BATTLER_README.md)
    └── INSTALL.md (rename from INSTALL_GUIDE.md)
```

---

## ✅ Installation Checklist

- [ ] Create folder: `/wp-content/plugins/pairwise-battler/`
- [ ] Upload: `pairwise-battler.php`
- [ ] Upload: `widget-class.php`
- [ ] Go to: WordPress Admin → Plugins
- [ ] Click: "Activate" on PairWise Battler
- [ ] Verify: "PairWise Battler" menu appears in sidebar
- [ ] Test: Open Elementor, search for widget
- [ ] Success! ✅

---

## 🔍 How to Identify Files

### Main Plugin File
```php
File: pairwise-battler.php

Header:
/**
 * Plugin Name: PairWise Battler
 * Description: Interactive image comparison widget...
 */

Size: ~25 KB
Lines: ~600
```

### Widget Class File
```php
File: widget-class.php

Start:
class PairWise_Battler_Widget extends \Elementor\Widget_Base

Size: ~35 KB
Lines: ~680
```

---

## 🎨 What Each File Does

### pairwise-battler.php
Contains:
- ✅ Plugin activation hook
- ✅ Database table creation
- ✅ REST API registration
- ✅ Admin menu creation
- ✅ Admin dashboard HTML
- ✅ CSV export functions
- ✅ Elementor widget registration
- ✅ All backend logic

### widget-class.php
Contains:
- ✅ Elementor widget class
- ✅ Widget controls (settings)
- ✅ Widget rendering (HTML output)
- ✅ JavaScript inline code
- ✅ Frontend functionality

---

## 📂 Directory Structure After Upload

```
/wp-content/
└── plugins/
    ├── elementor/                    (Elementor plugin)
    ├── pairwise-battler/             (Your new plugin)
    │   ├── pairwise-battler.php     ✅
    │   └── widget-class.php         ✅
    └── other-plugins/
```

---

## 🗄️ What Gets Created Automatically

### Database Tables (On Activation)
```sql
wp_pairwise_results
wp_pairwise_summary
```

### Admin Menu Item
```
WordPress Admin Sidebar
├── Dashboard
├── Posts
├── Media
├── Pages
├── Comments
├── PairWise Battler  ← NEW!
├── Appearance
└── Plugins
```

### REST API Endpoints
```
/wp-json/pairwise-battler/v1/save-results
/wp-json/pairwise-battler/v1/get-results
```

### Elementor Widget
```
Elementor Panel → Search
└── PairWise Battler ← NEW!
```

---

## 🚫 Common Mistakes to Avoid

### ❌ Don't Do This:
1. Upload all files including old ones
2. Create multiple folders
3. Upload to wrong location
4. Activate old plugins
5. Mix old and new files

### ✅ Do This Instead:
1. Upload only 2 required files
2. Create one folder: `pairwise-battler`
3. Upload to: `/wp-content/plugins/`
4. Activate only new plugin
5. Use only new files

---

## 🔄 Migration from Old Version

If you had old "Camera Battle" installed:

### Step 1: Export Data
```
WordPress Admin → Camera Battle
→ Export All Data (CSV)
→ Save backup
```

### Step 2: Deactivate Old
```
WordPress Admin → Plugins
→ Deactivate "Camera Battle Saver"
→ Deactivate "Camera Battle Widget"
```

### Step 3: Install New
```
Upload pairwise-battler.php
Upload widget-class.php
Activate "PairWise Battler"
```

### Step 4: Update Pages
```
Edit pages with Elementor
Remove old widget
Add new "PairWise Battler" widget
Reconfigure settings
```

---

## 📊 File Size Reference

```
pairwise-battler.php    ~25 KB  (main plugin)
widget-class.php        ~35 KB  (widget code)
────────────────────────────────
Total                   ~60 KB  (very lightweight!)
```

---

## 🎓 Learning Path

### For Quick Setup
1. Read: This file (FILE_GUIDE.md)
2. Read: INSTALL_GUIDE.md
3. Upload 2 files
4. Activate
5. Done!

### For Complete Understanding
1. Read: SINGLE_PLUGIN_SUMMARY.md
2. Read: PAIRWISE_BATTLER_README.md
3. Read: QUICK_REFERENCE.md
4. Experiment with widget
5. Check admin dashboard

---

## ✨ Pro Tips

### Fastest Setup (2 Minutes)
1. ⏱️ Create folder (10 sec)
2. ⏱️ Upload 2 files (30 sec)
3. ⏱️ Activate plugin (10 sec)
4. ⏱️ Add widget to page (1 min)
5. ⏱️ Test (10 sec)
✅ Done!

### Best Practices
1. Keep documentation files locally
2. Bookmark QUICK_REFERENCE.md
3. Export data regularly
4. Test after WordPress updates
5. Clear Elementor cache after changes

---

## 🎯 Final Answer

### "What do I upload?"

```
✅ pairwise-battler.php
✅ widget-class.php

That's it!
```

### "Where do I upload?"

```
/wp-content/plugins/pairwise-battler/
```

### "What about everything else?"

```
Documentation files: Keep for reference
Old camera-battle files: Delete or archive
HTML files: Not needed for WordPress plugin
```

---

## 🎉 You're Ready!

With just **2 files**, you have:
- ✅ Complete WordPress plugin
- ✅ Elementor widget
- ✅ Database storage
- ✅ Admin dashboard
- ✅ CSV export
- ✅ REST API
- ✅ All features

**Upload and activate!** 🚀

---

*Need help? See INSTALL_GUIDE.md for step-by-step instructions*
