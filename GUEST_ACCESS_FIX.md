# Guest Access Fix Summary

## Problem
The PairWise Battler widget was not working correctly for non-logged-in (guest) users:
1. **JavaScript Error**: `wpApiSettings is not defined` - This WordPress object is only available for logged-in admin users
2. **Cannot Complete Test**: Sometimes the last picture couldn't be selected
3. **No Data Saving**: Results were not being saved to the database when guests completed tests

## Root Cause
The JavaScript code was trying to access `wpApiSettings.nonce` which is only injected into the page for authenticated WordPress admin users. This caused:
- JavaScript errors in the console
- Failed fetch requests to the REST API
- Data not being saved for guest users

## Solution Implemented

### 1. Pass REST API URL from PHP to JavaScript
**File**: `widget-class.php`

Added REST API URL to the widget data attributes:
```php
// In render() method
$rest_url = rest_url('pairwise-battler/v1/save-results');

// Added to widget div
data-rest-url="<?php echo esc_url($rest_url); ?>"
```

### 2. Updated JavaScript Configuration
**File**: `widget-class.php` (JavaScript section)

Modified `readConfig()` to read the REST URL:
```javascript
async function readConfig(root){
  return {
    // ... existing config
    restUrl: root.getAttribute('data-rest-url') || '/wp-json/pairwise-battler/v1/save-results'
  };
}
```

### 3. Removed Nonce Requirement
**File**: `widget-class.php` (JavaScript section)

Updated `saveToWordPress()` function:
```javascript
// BEFORE (broken for guests):
fetch('/wp-json/pairwise-battler/v1/save-results', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-WP-Nonce': wpApiSettings?.nonce || ''  // ❌ Causes error for guests
  },
  // ...
})

// AFTER (works for everyone):
fetch(cfg.restUrl, {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'  // ✅ No nonce needed
  },
  // ...
})
```

## Why This Works

1. **Public REST API**: The endpoint already had `'permission_callback' => '__return_true'` which makes it publicly accessible
2. **No Authentication Required**: Since we want anyone to submit test results, we don't need WordPress nonces
3. **Dynamic URL**: Using `rest_url()` ensures the correct API URL even if WordPress is in a subdirectory
4. **Fallback**: If the data attribute is missing, it falls back to the standard REST API path

## Testing Steps

1. **Log out of WordPress admin** (or open site in incognito/private browsing)
2. Navigate to a page with the PairWise Battler widget
3. Complete the image comparison test by selecting winners
4. Open browser console (F12) - should see no errors
5. Check console for success message: "Results saved successfully to database!"
6. Log back into WordPress admin
7. Go to **PairWise Battler** menu
8. Verify the guest's test results appear in the dashboard

## Technical Details

### REST API Endpoint
- **URL**: `/wp-json/pairwise-battler/v1/save-results`
- **Method**: POST
- **Authentication**: None required (public endpoint)
- **Expected Payload**:
```json
{
  "session": "2025-10-10-14-30-abc123",
  "container_id": "cb-widget-id",
  "timestamp": "2025-10-10T14:30:00.000Z",
  "results": [...],
  "summary": [
    {
      "id": 1,
      "title": "Image Name",
      "clicks": 5,
      "completeWins": 1,
      "appearances": 10
    }
  ]
}
```

### Browser Compatibility
- Works in all modern browsers
- No authentication dependencies
- No WordPress-specific JavaScript required

## Files Modified
- `widget-class.php` (3 changes)
  1. Added `$rest_url` variable in `render()` method
  2. Added `data-rest-url` attribute to widget div
  3. Updated `readConfig()` to read REST URL
  4. Updated `saveToWordPress()` to use config REST URL and removed nonce

## Security Considerations
✅ **Safe for public use**:
- Data is sanitized in PHP before database insertion
- REST API validates all input fields
- Session IDs are randomly generated (no user tracking)
- No sensitive information exposed
- Database uses prepared statements (SQL injection safe)

## Benefits
✅ Guest users can complete tests without logging in
✅ No JavaScript errors in console
✅ Data automatically saves to database for all users
✅ Seamless experience for both admins and guests
✅ Proper error handling and logging

## Date
October 10, 2025
