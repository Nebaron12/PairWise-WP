# Camera Battle Admin Dashboard - User Guide

## Overview

The Camera Battle admin dashboard provides comprehensive analytics and reporting for your image comparison tests. Access it from the WordPress admin sidebar under "Camera Battle".

## Dashboard Layout

```
┌─────────────────────────────────────────────────────────┐
│  📸 Camera Battle Results                               │
│  View and analyze results from your image comparison tests│
├─────────────────────────────────────────────────────────┤
│                                                           │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐     │
│  │ Total Votes │  │  Sessions   │  │ Unique Users│     │
│  │     150     │  │      3      │  │     45      │     │
│  └─────────────┘  └─────────────┘  └─────────────┘     │
│                                                           │
├─────────────────────────────────────────────────────────┤
│  [Overall Results] [Per User Results] [Raw Vote Data]   │
├─────────────────────────────────────────────────────────┤
│  Select Session: [mkb-test-v1 ▼]     [Export CSV]       │
├─────────────────────────────────────────────────────────┤
│                                                           │
│  Aggregated Results for Session: mkb-test-v1             │
│                                                           │
│  ┌─────────────────────────────────────────────────┐   │
│  │ Rank │ Image    │ Clicks │ Complete │ Click %  │   │
│  │      │          │        │  Wins    │          │   │
│  ├──────┼──────────┼────────┼──────────┼──────────┤   │
│  │  #1  │ Camera A │   45   │    12    │  75.0%  │   │
│  │  #2  │ Camera C │   38   │     8    │  63.3%  │   │
│  │  #3  │ Camera B │   32   │     5    │  53.3%  │   │
│  │  #4  │ Camera D │   25   │     2    │  41.7%  │   │
│  └─────────────────────────────────────────────────┘   │
│                                                           │
└─────────────────────────────────────────────────────────┘
```

## Tab Descriptions

### 📊 Overall Results Tab
Shows combined statistics across all users who completed the test session.

**Columns:**
- **Rank**: Position based on total clicks
- **Image**: Image title/name
- **Total Clicks**: How many times this image was selected
- **Complete Wins**: Times this image won on the final comparison
- **Appearances**: Total times shown to users
- **Click Rate**: Percentage of times clicked when shown (color-coded)

**Color Coding:**
- 🟢 Green (60%+): High performance
- 🟡 Yellow (40-59%): Medium performance
- 🔴 Red (<40%): Low performance

### 👥 Per User Results Tab
Shows individual results for each user who completed the test.

**Features:**
- Results grouped by user (container_id)
- Completion timestamp for each user
- Individual rankings per user
- Useful for understanding user preferences

**Use Cases:**
- Identify outlier preferences
- Verify test completion
- Track individual user journeys

### 📝 Raw Vote Data Tab
Shows individual vote records (limit 500 most recent).

**Columns:**
- **Time**: When the vote occurred
- **User**: User identifier (container_id)
- **Image 1**: First image in comparison
- **Image 2**: Second image in comparison
- **Winner**: Which image was selected

**Use Cases:**
- Detailed analysis
- Data verification
- Debugging issues
- Export for external analysis

## Session Management

### Selecting a Session
The session dropdown shows:
- Session ID/Name
- Number of votes in that session
- Date of most recent activity

**Example:**
```
mkb-test-v1 (150 votes, Oct 6, 2025)
summer-2025-test (87 votes, Sep 15, 2025)
```

### Session Best Practices
- Use descriptive session names (set in HTML widget via `data-session`)
- Create new sessions for different test groups
- Keep sessions organized by date or campaign name

## Exporting Data

Click the **Export CSV** button to download:

1. **Summary Data Section**
   - User-by-user breakdown
   - Clicks, complete wins, and appearances per image
   - Timestamps

2. **Raw Vote Data Section**
   - Every individual vote
   - Complete comparison details
   - Chronological order

**CSV Format:**
```csv
SUMMARY DATA
User,Image,Clicks,Complete Wins,Appearances,Timestamp
camera-battle-1,Camera A,5,1,10,2025-10-06 14:23:00


RAW VOTE DATA
Timestamp,User,Image 1,Image 2,Winner
2025-10-06 14:23:00,camera-battle-1,Camera A,Camera B,Camera A
```

## Understanding the Metrics

### Clicks vs Complete Wins
- **Clicks**: Total times an image was selected in ANY comparison
- **Complete Wins**: Only counted when the image wins the FINAL available comparison
- Complete Wins is a more meaningful measure of overall preference

### Click Rate
- Formula: `(Total Clicks / Total Appearances) × 100`
- Shows how compelling the image is when shown
- Higher percentage = more consistently chosen

### Appearances
- How many times the image was shown to users
- Should be roughly equal across all images
- Variations occur due to elimination patterns

## Tips for Analysis

### Finding the Best Image
1. Look at **Complete Wins** first (most definitive)
2. Check **Click Rate** for consistency
3. Review **Total Clicks** for volume

### Identifying Issues
- Very low appearances = possible technical issue
- 0% click rate = image might not be loading
- Huge discrepancy between users = test design issue

### Statistical Significance
- Need at least 30-50 users for reliable results
- More comparisons = more reliable data
- Consider running multiple sessions and comparing

## Troubleshooting

### No Data Showing
1. Verify users have completed the test
2. Check that the plugin is activated
3. Ensure the session name matches the HTML widget
4. Look in Raw Data tab to verify data is being saved

### Users Not in Per User Tab
- User must complete ALL comparisons to appear
- Check Raw Data tab for partial sessions

### Export Not Working
- Verify you're logged in as an admin
- Check browser console for errors
- Try a different browser

## Database Tables

The plugin creates two tables:

1. **wp_camera_battle_results**
   - Individual vote records
   - One row per image comparison

2. **wp_camera_battle_summary**
   - Aggregated session data
   - One row per image per user

### Direct Database Access
You can query these tables directly from phpMyAdmin or WordPress database tools:

```sql
-- Get overall winners
SELECT image_title, SUM(complete_wins) as total_complete_wins
FROM wp_camera_battle_summary
WHERE session_id = 'mkb-test-v1'
GROUP BY image_title
ORDER BY total_complete_wins DESC;
```

## Privacy Considerations

- User data is stored by container_id (randomly generated)
- No personal information is collected by default
- Timestamps are stored for analysis
- Consider GDPR compliance if deploying in EU
- Add privacy notice to your test page

## Support & Customization

For customization:
- Edit `camera-battle-saver.php` for backend changes
- Modify CSS in `get_admin_css()` function
- Add new tabs by creating new render methods
- Extend REST API for additional features
