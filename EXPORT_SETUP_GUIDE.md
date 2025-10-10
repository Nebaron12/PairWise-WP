# Setup Instructions for CSV Export Fix

## Quick Setup

### Step 1: Update Database
Since we added a new table, you need to reactivate the plugin:

1. Go to WordPress Admin → **Plugins**
2. Find **PairWise Battler**
3. Click **Deactivate**
4. Wait a moment
5. Click **Activate**

✅ This creates the new `wp_pairwise_battles` table automatically.

### Step 2: Test the Export
1. Go to **PairWise Battler** in admin menu
2. Click **Export All Data** button
3. You should get a CSV file download (not HTML)
4. Open the CSV - you'll see columns: Session ID, Image 1, Image 2, Winner, Timestamp

### Step 3: Generate New Test Data (Optional)
Since the new battle-by-battle format only applies to NEW tests:

1. Log out (or use incognito mode)
2. Go to your page with the widget
3. Complete a full test
4. Go back to admin and export CSV
5. You should see the individual battle rows for your new test

## What Changed

### Admin Dashboard
- **No changes** - Still shows aggregated stats (total clicks, wins per image)
- Works exactly the same as before

### CSV Exports
- **Format changed** - Now shows individual battles instead of summary stats
- **Each row** = One comparison (Image A vs Image B, winner selected)
- **Fixed download** - Actually downloads CSV file instead of HTML page

## Troubleshooting

### "Export still downloads HTML"
- Make sure you deactivated and reactivated the plugin
- Clear browser cache
- Try in incognito/private window

### "CSV is empty after export"
- Old test data before this update won't have battle records
- Complete a new test to generate battle data
- Summary data is still there (check admin dashboard)

### "Database error on activation"
- Check WordPress database permissions
- Make sure your MySQL user can CREATE TABLE
- Check error log: `wp-content/debug.log`

## Manual Database Creation (If Needed)

If automatic table creation fails, run this SQL in phpMyAdmin:

```sql
CREATE TABLE IF NOT EXISTS wp_pairwise_battles (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    session_id varchar(255) NOT NULL,
    image1_title varchar(255) NOT NULL,
    image2_title varchar(255) NOT NULL,
    winner_title varchar(255) NOT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
    PRIMARY KEY (id),
    KEY session_id (session_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Note**: Replace `wp_` with your actual WordPress table prefix if different.

## Verification Checklist

- [ ] Plugin deactivated and reactivated
- [ ] Export All Data downloads a `.csv` file
- [ ] CSV opens correctly in Excel/Sheets
- [ ] CSV has columns: Session ID, Image 1, Image 2, Winner, Timestamp
- [ ] New tests save data correctly
- [ ] Guest users can complete tests
- [ ] Admin dashboard still works

## Need Help?

Check `CSV_EXPORT_FIX.md` for detailed technical information.
