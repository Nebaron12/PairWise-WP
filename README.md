# Camera Battle Widget - Implementation Guide

## Changes Implemented

### 1. Clicks vs Complete Wins

The code now tracks two separate metrics:

- **Clicks**: Every time an image is selected (previously called "wins")
- **Complete Wins**: Only incremented when an image wins on the final available comparison

The state object now includes:
```javascript
{
  clicks: {},        // renamed from wins
  completeWins: {},  // new - only for final comparison wins
  appearances: {},
  results: []
}
```

### 2. WordPress Database Integration

The results are now saved to the WordPress database instead of just downloading as CSV.

#### Database Tables Created:

1. **wp_camera_battle_results** - Stores individual battle results
   - session_id
   - container_id
   - timestamp
   - battle_timestamp
   - image1_id, image1_title
   - image2_id, image2_title
   - winner_id, winner_title

2. **wp_camera_battle_summary** - Stores aggregated session data
   - session_id
   - container_id
   - timestamp
   - image_id, image_title
   - clicks (total times clicked)
   - complete_wins (wins on final comparison)
   - appearances (total times shown)

## Installation Steps

### Step 1: Install the WordPress Plugin

1. Upload `camera-battle-saver.php` to your WordPress plugins directory:
   - Via FTP: `/wp-content/plugins/camera-battle-saver/camera-battle-saver.php`
   - Or upload as a ZIP file through WordPress admin

2. Activate the plugin in WordPress admin:
   - Go to **Plugins** → **Installed Plugins**
   - Find "Camera Battle Results Saver"
   - Click **Activate**

3. The plugin will automatically create the database tables upon activation.

### Step 2: Add the HTML Widget to Elementor

1. Edit your page in Elementor
2. Add an **HTML Widget**
3. Paste the entire contents of `Main.html` into the widget
4. Save and publish

### Step 3: Verify the Setup

1. Test the widget by completing all image comparisons
2. Click the "Download CSV" button (now saves to database)
3. Check if you see a success message

## Fallback Mechanism

If the WordPress database save fails for any reason, the code will automatically fall back to downloading a CSV file instead. This ensures no data is lost.

## Viewing Saved Data

You can query the data directly from the WordPress database:

```sql
-- View all battle results
SELECT * FROM wp_camera_battle_results ORDER BY timestamp DESC;

-- View summary with clicks and complete wins
SELECT * FROM wp_camera_battle_summary ORDER BY timestamp DESC;

-- Get summary for a specific session
SELECT * FROM wp_camera_battle_summary 
WHERE session_id = 'mkb-test-v1' 
ORDER BY clicks DESC;
```

Or use the REST API endpoint:

```
GET /wp-json/camera-battle/v1/get-results/mkb-test-v1
```

## REST API Endpoint

The plugin creates a REST API endpoint at:
```
POST /wp-json/camera-battle/v1/save-results
```

Payload structure:
```json
{
  "session": "session-name",
  "container_id": "camera-battle-1",
  "timestamp": "2025-10-06T12:00:00.000Z",
  "results": [...],
  "summary": [
    {
      "id": 1,
      "title": "Camera A",
      "clicks": 5,
      "completeWins": 1,
      "appearances": 10
    }
  ]
}
```

## Updated Display Table

The completion table now shows:
- Image name
- **Clicks** (total times clicked)
- **Complete Wins** (wins on final comparison)
- Appearances
- Click % (percentage of clicks vs appearances)

## Troubleshooting

### Database Save Fails
1. Check that the plugin is activated
2. Verify database tables exist: `wp_camera_battle_results` and `wp_camera_battle_summary`
3. Check WordPress REST API is enabled
4. Look in browser console for error messages

### CSV Download Fallback
If you see CSV downloading instead of database save, check:
- WordPress REST API is accessible
- No CORS issues (should work on same domain)
- Plugin is activated properly

## Security Notes

- The REST API endpoint is publicly accessible (necessary for frontend to save)
- All input is sanitized before database insertion
- Uses WordPress nonce for additional security
- SQL injection protection via prepared statements

## Future Enhancements

Consider adding:
- Admin dashboard to view results
- Data export functionality in WordPress admin
- Automatic cleanup of old session data
- Analytics and reporting features
