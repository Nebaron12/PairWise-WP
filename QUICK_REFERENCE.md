# 🎯 PairWise Battler - Quick Reference Card

## 📥 Installation (60 Seconds)

```
1. Create folder: /wp-content/plugins/pairwise-battler/
2. Upload: pairwise-battler.php and widget-class.php
3. Activate in WordPress Plugins
✅ Done!
```

---

## 🎨 Add to Page (2 Minutes)

```
1. Edit page with Elementor
2. Search: "PairWise Battler"
3. Drag widget to page
4. Add images (min 2)
5. Publish
✅ Done!
```

---

## 📊 View Results

```
WordPress Admin → PairWise Battler
  ├─ Overall Results (all data)
  └─ Per-User Results (by session)
```

---

## ⚙️ Widget Settings

### Content Tab
- Heading text
- Completion message
- Button text

### Images Tab
- Add/remove images
- Image titles
- Drag to reorder

### Settings Tab
- ☐ Shuffle images
- ☐ Show progress bar
- Session name
- Webhook URL

### Style Tab
- Colors (primary, background, text)
- Typography
- Border radius
- Spacing

---

## 🔑 Key Features

| Feature | Description |
|---------|-------------|
| **Auto-Save** | Results saved automatically |
| **Session Tracking** | One vote per device |
| **Mobile Ready** | Fully responsive |
| **CSV Export** | Download data anytime |
| **Admin Dashboard** | View analytics |
| **REST API** | Integrate with other tools |

---

## 📐 Database Tables

```sql
wp_pairwise_results    -- Individual votes
wp_pairwise_summary    -- Session summaries
```

Created automatically on activation.

---

## 🌐 API Endpoints

```
POST /wp-json/pairwise-battler/v1/save-results
GET  /wp-json/pairwise-battler/v1/get-results
```

---

## 🎯 Data Tracked

| Metric | Description |
|--------|-------------|
| **Clicks** | Times image was selected |
| **Complete Wins** | Times image won comparison |
| **Session ID** | YYYY-MM-DD-HH-MM-UniqueID |
| **Timestamp** | When test was completed |

---

## 💡 Use Cases

✅ A/B Testing  
✅ Product Selection  
✅ Design Decisions  
✅ Photography Contests  
✅ Marketing Campaigns  
✅ Team Voting  

---

## 🔧 Troubleshooting

| Problem | Solution |
|---------|----------|
| Widget not found | Deactivate/reactivate plugin |
| Images not saving | Check REST API: /wp-json/ |
| No results in admin | Complete at least one test |
| Permission error | Login as Administrator |

---

## 📦 Requirements

- ✅ WordPress 5.0+
- ✅ Elementor 3.0+
- ✅ PHP 7.0+
- ✅ MySQL database

---

## 🎨 Quick Customization

### Change Colors
```css
.pw-card { background: #f0f0f0; }
.pw-button { background: #667eea; }
```

### Larger Images
```css
.pw-card { width: 400px; }
.pw-result-img { width: 150px; }
```

### Custom Hover
```css
.pw-card:hover {
    transform: scale(1.05);
    transition: 0.3s;
}
```

---

## 📁 File Structure

```
pairwise-battler/
├── pairwise-battler.php  (main plugin)
└── widget-class.php      (widget code)
```

---

## 🔐 Security

✅ SQL injection prevention  
✅ XSS protection  
✅ CSRF protection  
✅ Input sanitization  
✅ Output escaping  
✅ Nonce verification  

---

## 📈 Performance

- **Database**: Indexed, optimized
- **File Size**: ~60 KB total
- **Load Time**: Minimal impact
- **Scalability**: Thousands of sessions

---

## 🎓 Learning Resources

| Document | Purpose |
|----------|---------|
| `INSTALL_GUIDE.md` | Step-by-step setup |
| `PAIRWISE_BATTLER_README.md` | Full documentation |
| `SINGLE_PLUGIN_SUMMARY.md` | Overview |
| `PACKAGING_GUIDE.md` | Distribution info |

---

## ⚡ Quick Commands

### Check Database Tables
```sql
SHOW TABLES LIKE 'wp_pairwise%';
```

### Check API
```
Visit: https://yoursite.com/wp-json/pairwise-battler/v1/
```

### Clear Elementor Cache
```
Elementor → Tools → Regenerate CSS
```

---

## 🎯 Success Metrics

After installation, you should see:
- ✅ "PairWise Battler" in WordPress admin sidebar
- ✅ Widget in Elementor panel
- ✅ Two database tables created
- ✅ REST API endpoint accessible

---

## 🚀 Workflow

```
1. Install plugin
   ↓
2. Add widget to page
   ↓
3. Configure images & settings
   ↓
4. Publish page
   ↓
5. Share with users
   ↓
6. Collect votes
   ↓
7. Analyze in dashboard
   ↓
8. Make data-driven decision!
```

---

## 📞 Support Checklist

Before asking for help:
- [ ] Elementor is activated
- [ ] PHP version is 7.0+
- [ ] WordPress version is 5.0+
- [ ] Plugin is activated
- [ ] Browser console checked (F12)
- [ ] REST API tested (/wp-json/)

---

## 🎨 Default Widget Settings

```
Heading: "Which photo looks better?"
Completion: "All battles complete! Thanks for voting."
Reset Button: "Restart"
Shuffle: OFF
Progress Bar: ON
Session Name: "Default"
Primary Color: #667eea
```

---

## 📊 Admin Dashboard Features

**Overall Results Tab:**
- Aggregate data
- Ranking by wins
- Click rate percentage
- Export all button
- Winner highlighted

**Per-User Results Tab:**
- Session filter dropdown
- Columnar layout
- Result numbering
- Totals row
- Export session button

---

## 🎉 Quick Wins

Get results in **5 minutes**:
1. Upload 2 files (30 sec)
2. Activate plugin (10 sec)
3. Add widget (1 min)
4. Add 2 images (2 min)
5. Publish (10 sec)
6. Test vote (1 min)
✅ See results in admin!

---

## 💾 Backup Recommendation

Before major changes:
1. Export all data (CSV)
2. Backup WordPress database
3. Keep plugin files

Data stored in:
- `wp_pairwise_results`
- `wp_pairwise_summary`

---

## 🔄 Update Process

1. Deactivate plugin
2. Replace files
3. Reactivate plugin
4. Clear caches
5. Test functionality

Database preserved automatically.

---

## 📮 CSV Export Format

**Single Session:**
```
Image Name, Clicks, Complete Wins
Option A, 5, 3
Option B, 3, 1
```

**All Data:**
```
Session ID, Image Name, Clicks, Complete Wins, Created At
2025-10-08-14-30-abc, Option A, 5, 3, 2025-10-08 14:35:00
```

---

## 🎯 Best Practices

- Use 2-6 images for best UX
- Clear image titles
- Descriptive session names
- Regular data exports
- Mobile testing
- Image optimization

---

## 📱 Device Compatibility

| Platform | Status |
|----------|--------|
| Desktop | ✅ Full support |
| Tablet | ✅ Responsive |
| Mobile | ✅ Touch-friendly |
| iPhone | ✅ Tested |
| Android | ✅ Tested |

---

## 🌟 Pro Tips

1. **Shuffle ON** = More objective results
2. **Session Names** = Easy filtering
3. **Export Often** = Data backups
4. **Mobile Test** = Verify UX
5. **Clear Titles** = Better analytics

---

**Need more details?** See `PAIRWISE_BATTLER_README.md`

**Need help installing?** See `INSTALL_GUIDE.md`

---

*PairWise Battler - Make Better Decisions with Data* 🎯
