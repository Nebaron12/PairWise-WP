# CSV Export Fix & Battle Results Enhancement

## Issues Fixed

### 1. CSV Export Downloads HTML Instead of CSV
**Problem**: Export buttons were downloading the admin panel HTML instead of actual CSV files.

**Root Cause**: 
- Missing `exit;` statement after CSV output
- Output buffering not cleared before sending CSV headers
- WordPress was rendering the admin page after CSV output

**Solution**:
- Added `ob_end_clean()` to clear output buffers
- Added `exit;` after CSV output to prevent further rendering
- Added proper CSV headers with UTF-8 BOM for Excel compatibility
- Added `Pragma` and `Expires` headers for proper download behavior

### 2. Export Format Changed to Individual Battles
**Problem**: CSV was exporting aggregated summary stats (total clicks per image) instead of individual comparison results.

**User Requirement**: Export should show each comparison battle as a separate row showing which two images were compared and which one won.

**Solution**:
- Created new database table `wp_pairwise_battles` to store individual battle results
- Modified `save_results()` to save both summary data (for admin dashboard) and battle data (for export)
- Updated export functions to use battles table instead of summary table

## Database Changes

### New Table: `wp_pairwise_battles`
Stores individual battle results for CSV export:

```sql
CREATE TABLE wp_pairwise_battles (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    session_id varchar(255) NOT NULL,
    image1_title varchar(255) NOT NULL,
    image2_title varchar(255) NOT NULL,
    winner_title varchar(255) NOT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
    PRIMARY KEY (id),
    KEY session_id (session_id)
);
```

### Existing Table: `wp_pairwise_summary`
Continues to store aggregated data for admin dashboard display (unchanged).

## Code Changes

### 1. Plugin Activation (`activate()`)
**File**: `pairwise-battler.php`

Added creation of `pairwise_battles` table alongside existing `pairwise_summary` table.

### 2. Save Results API (`save_results()`)
**File**: `pairwise-battler.php`

**Before**: Only saved summary data (total clicks per image)
```php
// Old: Only summary data
foreach ($summary as $item) {
    $wpdb->insert($table_summary, [...]);
}
```

**After**: Saves both summary (for dashboard) and battles (for export)
```php
// New: Summary data for dashboard
foreach ($summary as $item) {
    $wpdb->insert($table_summary, [...]);
}

// New: Individual battle results for export
foreach ($results as $battle) {
    $wpdb->insert($table_battles, [
        'session_id' => $session_id,
        'image1_title' => $battle['image1_title'],
        'image2_title' => $battle['image2_title'],
        'winner_title' => $battle['winner_title']
    ]);
}
```

### 3. Export Session CSV (`export_session_csv()`)
**File**: `pairwise-battler.php`

**Changes**:
- Changed query from `pairwise_summary` to `pairwise_battles` table
- Added `ob_end_clean()` to clear output buffer
- Added UTF-8 BOM for Excel compatibility
- Added `exit;` to prevent HTML rendering
- Changed CSV columns to: Session ID, Image 1, Image 2, Winner, Timestamp

### 4. Export All CSV (`export_all_csv()`)
**File**: `pairwise-battler.php`

**Changes**:
- Changed query from `pairwise_summary` to `pairwise_battles` table
- Added `ob_end_clean()` to clear output buffer
- Added UTF-8 BOM for Excel compatibility
- Added `exit;` to prevent HTML rendering
- Changed CSV columns to: Session ID, Image 1, Image 2, Winner, Timestamp

## New CSV Export Format

### Previous Format (Aggregated Stats)
```csv
Session ID,Image Name,Clicks,Complete Wins,Created At
2025-10-10-14-30-abc123,Image A,5,1,2025-10-10 14:35:00
2025-10-10-14-30-abc123,Image B,3,0,2025-10-10 14:35:00
2025-10-10-14-30-abc123,Image C,2,0,2025-10-10 14:35:00
```

### New Format (Individual Battles)
```csv
Session ID,Image 1,Image 2,Winner,Timestamp
2025-10-10-14-30-abc123,Image A,Image B,Image A,2025-10-10 14:30:15
2025-10-10-14-30-abc123,Image A,Image C,Image A,2025-10-10 14:30:22
2025-10-10-14-30-abc123,Image B,Image C,Image B,2025-10-10 14:30:28
2025-10-10-14-30-abc123,Image A,Image B,Image A,2025-10-10 14:30:35
... (one row per comparison)
```

## Installation Steps

### For Existing Installations:
1. **Deactivate** the plugin in WordPress admin
2. **Replace** the plugin files with the updated version
3. **Activate** the plugin again
   - This will automatically create the new `wp_pairwise_battles` table
   - Existing `wp_pairwise_summary` data is preserved

### Note on Existing Data:
- ⚠️ **Old test results will NOT have battle data** (only new tests after this update)
- Summary data (admin dashboard) will continue to work for all historical data
- CSV exports will only include battle-by-battle data for tests completed after this update
- If you need historical battle data, it cannot be reconstructed from summary data

## Testing Steps

### Test CSV Export Functionality:
1. Log into WordPress admin
2. Go to **PairWise Battler** menu
3. Click **Overall Results** tab
4. Click **Export All Data** button
5. ✅ Should download a `.csv` file (not HTML)
6. Open CSV in Excel/Sheets
7. ✅ Should show columns: Session ID, Image 1, Image 2, Winner, Timestamp
8. ✅ Each row should represent one comparison battle

### Test Per-Session Export:
1. Go to **Per User Results** tab
2. Select a session from dropdown
3. Click **Export CSV** button
4. ✅ Should download a `.csv` file for that specific session
5. ✅ Should contain only battles from selected session

### Test Data Saving (Guest Users):
1. Log out or use incognito mode
2. Go to page with widget
3. Complete a full test (select winners for all comparisons)
4. Log back into admin
5. Check **Overall Results** - should see aggregated stats
6. Export CSV - should see individual battle rows for this test

## Technical Details

### Why Two Tables?
- **`pairwise_summary`**: Fast aggregated queries for dashboard (clicks, wins per image)
- **`pairwise_battles`**: Detailed export data (every single comparison)
- Separating concerns improves dashboard performance while maintaining detailed export capability

### CSV Headers Explained:
```php
header('Content-Type: text/csv; charset=utf-8');  // Tell browser it's CSV with UTF-8
header('Content-Disposition: attachment; filename="..."'); // Trigger download
header('Pragma: no-cache');    // Don't cache the download
header('Expires: 0');          // Expire immediately
```

### UTF-8 BOM:
```php
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
```
- Byte Order Mark tells Excel the file is UTF-8
- Prevents character encoding issues with special characters

### Output Buffer Clearing:
```php
if (ob_get_level()) {
    ob_end_clean();
}
```
- WordPress often has output buffering active
- Must clear before sending CSV headers
- Prevents "headers already sent" errors

## Files Modified
1. `pairwise-battler.php`:
   - `activate()` - Added battles table creation
   - `save_results()` - Save both summary and battle data
   - `export_session_csv()` - Changed to export battles, fixed CSV output
   - `export_all_csv()` - Changed to export battles, fixed CSV output

## Benefits

✅ CSV exports now work correctly (downloads CSV, not HTML)
✅ Export format shows individual comparisons (as requested)
✅ Admin dashboard unchanged (still shows aggregated stats)
✅ Excel-compatible UTF-8 encoding
✅ Backwards compatible (existing dashboard data preserved)
✅ Guest users can complete tests and data saves correctly
✅ Detailed battle history available for analysis

## Data Analysis Examples

With the new format, you can analyze:
- **Head-to-head win rates**: Filter battles where Image A vs Image B
- **Temporal patterns**: See which images win at different times
- **User decision sequences**: Analyze order of battles per session
- **Consistency**: Compare same matchup across different sessions
- **Export to analytics tools**: CSV format ready for R, Python, Excel, etc.

## Date
October 10, 2025
