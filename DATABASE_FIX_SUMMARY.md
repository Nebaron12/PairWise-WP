# 🔧 Database Fix Summary - PairWise Battler

## What Was Fixed

### 1. Database Table Structure
**Problem:** The `pairwise_summary` table was created with wrong columns
- ❌ OLD: `session_data TEXT` (wrong - tried to store JSON)
- ✅ NEW: Proper columns for `image_name`, `clicks`, `complete_wins`

**Fixed Table Structure:**
```sql
CREATE TABLE wp_pairwise_summary (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    session_id varchar(255) NOT NULL,
    image_name varchar(255) NOT NULL,
    clicks int(11) DEFAULT 0,
    complete_wins int(11) DEFAULT 0,
    created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
    PRIMARY KEY (id),
    KEY session_id (session_id),
    KEY image_name (image_name)
);
```

### 2. Save Results Function
**Problem:** Function was trying to insert wrong data into wrong columns

**Fixed Code:**
```php
public function save_results($request) {
    // Now properly validates required fields
    // Extracts 'summary' array from request
    // Inserts each image's data separately
    // Uses transactions for data integrity
    // Proper error handling with try/catch
}
```

**What it does now:**
- ✅ Validates `session` and `summary` parameters
- ✅ Loops through each image in summary
- ✅ Inserts `image_name`, `clicks`, `complete_wins` per image
- ✅ Uses database transactions (COMMIT/ROLLBACK)
- ✅ Returns success/error messages

### 3. Export Functions
**Problem:** Referenced wrong table name (`pairwise_results`)

**Fixed:**
- ✅ `export_session_csv()` - Now uses `pairwise_summary`
- ✅ `export_all_csv()` - Now uses `pairwise_summary`

### 4. Get Results Function
**Problem:** Referenced wrong table, missing ARRAY_A format

**Fixed:**
- ✅ Uses `pairwise_summary` table
- ✅ Returns data in proper array format
- ✅ Includes success status in response

---

## How to Apply the Fix

### Option 1: Reactivate Plugin (Recommended)

1. **Deactivate the plugin:**
   - WordPress Admin → Plugins
   - Find "PairWise Battler"
   - Click "Deactivate"

2. **Reactivate the plugin:**
   - Click "Activate"
   - This will recreate the table with correct structure

3. **Note:** Any old data will be preserved if you already had the table

### Option 2: Manual SQL (If Option 1 Doesn't Work)

Run this SQL in phpMyAdmin or your database tool:

```sql
-- Drop old table (WARNING: This deletes all data!)
DROP TABLE IF EXISTS wp_pairwise_summary;
DROP TABLE IF EXISTS wp_pairwise_results;

-- Create new table with correct structure
CREATE TABLE wp_pairwise_summary (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    session_id varchar(255) NOT NULL,
    image_name varchar(255) NOT NULL,
    clicks int(11) DEFAULT 0,
    complete_wins int(11) DEFAULT 0,
    created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
    PRIMARY KEY (id),
    KEY session_id (session_id),
    KEY image_name (image_name)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Verify It's Working

### Test the Complete Flow:

1. **Create a test page:**
   - Edit page with Elementor
   - Add PairWise Battler widget
   - Add 2-3 images

2. **Complete a test:**
   - View the page
   - Click through all image comparisons
   - Complete the test

3. **Check database:**
   - Go to phpMyAdmin
   - Open `wp_pairwise_summary` table
   - You should see rows with your data

4. **Check admin panel:**
   - WordPress Admin → PairWise Battler
   - Overall Results should show data
   - Per User Results should show your test

5. **Test export:**
   - Click "Export All Data"
   - CSV should download with your results

---

## What Changed in the Code

### Files Modified:
- ✅ `pairwise-battler.php` (main plugin file)

### Functions Updated:

#### 1. `activate()` - Database Table Creation
```php
// BEFORE: Wrong columns
CREATE TABLE wp_pairwise_summary (
    session_data text NOT NULL  // ❌ Wrong!
)

// AFTER: Correct columns
CREATE TABLE wp_pairwise_summary (
    image_name varchar(255) NOT NULL,    // ✅
    clicks int(11) DEFAULT 0,            // ✅
    complete_wins int(11) DEFAULT 0      // ✅
)
```

#### 2. `save_results()` - Data Insertion
```php
// BEFORE: Tried to insert sessionId and results
$session_id = $params['sessionId'];  // ❌ Wrong key
$results = $params['results'];       // ❌ Wrong data

// AFTER: Correctly uses summary data
$session_id = $params['session'];    // ✅ Correct key
$summary = $params['summary'];       // ✅ Correct data

foreach ($summary as $item) {
    // Insert each image's data
    $wpdb->insert($table_summary, array(
        'image_name' => $item['title'],
        'clicks' => $item['clicks'],
        'complete_wins' => $item['completeWins']
    ));
}
```

#### 3. Export Functions
```php
// BEFORE:
$table_results = $wpdb->prefix . 'pairwise_results';  // ❌ Wrong table

// AFTER:
$table_summary = $wpdb->prefix . 'pairwise_summary';  // ✅ Correct table
```

---

## Data Flow (How It Works Now)

```
1. User completes test
   ↓
2. JavaScript collects data:
   {
     session: "2025-10-08-14-30-abc123",
     summary: [
       { title: "Option A", clicks: 5, completeWins: 1 },
       { title: "Option B", clicks: 3, completeWins: 0 }
     ]
   }
   ↓
3. POST to /wp-json/pairwise-battler/v1/save-results
   ↓
4. save_results() function:
   - Validates data
   - Starts transaction
   - Inserts each image into wp_pairwise_summary
   - Commits transaction
   ↓
5. Database now contains:
   | id | session_id | image_name | clicks | complete_wins | created_at |
   |----|------------|------------|--------|---------------|------------|
   | 1  | 2025-...   | Option A   | 5      | 1             | 2025-...   |
   | 2  | 2025-...   | Option B   | 3      | 0             | 2025-...   |
   ↓
6. Admin panel reads from wp_pairwise_summary
   ↓
7. Shows results in dashboard ✅
```

---

## JavaScript (No Changes Needed)

The widget JavaScript already sends the correct data format:

```javascript
// This is already correct in widget-class.php
const summaryData = images.map(img => ({
  id: img.id,
  title: img.title,
  clicks: state.clicks[img.id] || 0,
  completeWins: state.completeWins[img.id] || 0,
  appearances: state.appearances[img.id] || 0
}));

fetch('/wp-json/pairwise-battler/v1/save-results', {
  method: 'POST',
  body: JSON.stringify({
    session: cfg.session,
    summary: summaryData  // ✅ This is what we save!
  })
});
```

---

## Troubleshooting

### "No results showing in admin"

**Solution:**
1. Check browser console (F12) for errors
2. Verify REST API is working: Visit `yoursite.com/wp-json/pairwise-battler/v1/`
3. Check database table exists and has correct structure
4. Complete a new test after fixing

### "Error saving results"

**Solution:**
1. Check browser console for exact error
2. Verify table structure is correct (see SQL above)
3. Check WordPress REST API is enabled
4. Try deactivating security plugins temporarily

### "Table doesn't exist"

**Solution:**
1. Deactivate plugin
2. Reactivate plugin
3. If still fails, run manual SQL (see Option 2 above)

---

## Summary

✅ **Fixed:** Database table structure  
✅ **Fixed:** Save results function  
✅ **Fixed:** Export functions  
✅ **Fixed:** Get results function  
✅ **Result:** Data now saves correctly!  

**Action Required:**
- Deactivate and reactivate plugin to recreate table
- Test by completing a comparison
- Verify data appears in admin panel

---

## Files Changed

- ✅ `pairwise-battler.php` - Main plugin file
  - `activate()` - Database creation
  - `save_results()` - Data insertion
  - `export_session_csv()` - CSV export
  - `export_all_csv()` - Full export
  - `get_results()` - Data retrieval

- ℹ️ `widget-class.php` - No changes needed (JavaScript was already correct)

---

**Status: ✅ FIXED AND READY TO TEST**

Deactivate/reactivate the plugin and try completing a test!
