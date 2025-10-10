<?php
if (!defined('ABSPATH')) {
    exit;
}

class pairwise_battler_Widget extends \Elementor\Widget_Base {
    
    public function get_name() {
        return 'pairwise_battler';
    }
    
    public function get_title() {
        return esc_html__('PairWise Battler', 'pairwise-battler');
    }
    
    public function get_icon() {
        return 'eicon-image-before-after';
    }
    
    public function get_categories() {
        return ['general'];
    }
    
    public function get_keywords() {
        return ['pairwise', 'comparison', 'vote', 'image', 'test', 'preference', 'battle'];
    }
    
    protected function register_controls() {
        
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Content', 'pairwise-battler'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'heading_text',
            [
                'label' => esc_html__('Heading Text', 'pairwise-battler'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Which photo looks better?', 'pairwise-battler'),
                'placeholder' => esc_html__('Enter your heading', 'pairwise-battler'),
            ]
        );
        
        $this->add_control(
            'completion_text',
            [
                'label' => esc_html__('Completion Text', 'pairwise-battler'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('All battles complete! Thanks for voting.', 'pairwise-battler'),
                'placeholder' => esc_html__('Enter completion message', 'pairwise-battler'),
            ]
        );
        
        $this->add_control(
            'reset_button_text',
            [
                'label' => esc_html__('Reset Button Text', 'pairwise-battler'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Restart', 'pairwise-battler'),
                'placeholder' => esc_html__('Enter button text', 'pairwise-battler'),
            ]
        );
        
        $this->end_controls_section();
        
        // Images Section
        $this->start_controls_section(
            'images_section',
            [
                'label' => esc_html__('Images', 'pairwise-battler'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $repeater = new \Elementor\Repeater();
        
        $repeater->add_control(
            'image',
            [
                'label' => esc_html__('Choose Image', 'pairwise-battler'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );
        
        $repeater->add_control(
            'image_title',
            [
                'label' => esc_html__('Image Title', 'pairwise-battler'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Image', 'pairwise-battler'),
                'placeholder' => esc_html__('Enter image title', 'pairwise-battler'),
            ]
        );
        
        $this->add_control(
            'images_list',
            [
                'label' => esc_html__('Images', 'pairwise-battler'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'image_title' => esc_html__('Option A', 'pairwise-battler'),
                        'image' => ['url' => \Elementor\Utils::get_placeholder_image_src()],
                    ],
                    [
                        'image_title' => esc_html__('Option B', 'pairwise-battler'),
                        'image' => ['url' => \Elementor\Utils::get_placeholder_image_src()],
                    ],
                    [
                        'image_title' => esc_html__('Option C', 'pairwise-battler'),
                        'image' => ['url' => \Elementor\Utils::get_placeholder_image_src()],
                    ],
                    [
                        'image_title' => esc_html__('Option D', 'pairwise-battler'),
                        'image' => ['url' => \Elementor\Utils::get_placeholder_image_src()],
                    ],
                ],
                'title_field' => '{{{ image_title }}}',
            ]
        );
        
        $this->end_controls_section();
        
        // Settings Section
        $this->start_controls_section(
            'settings_section',
            [
                'label' => esc_html__('Settings', 'pairwise-battler'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'shuffle',
            [
                'label' => esc_html__('Randomize Order', 'pairwise-battler'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'pairwise-battler'),
                'label_off' => esc_html__('No', 'pairwise-battler'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'show_progress',
            [
                'label' => esc_html__('Show Progress Bar', 'pairwise-battler'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'pairwise-battler'),
                'label_off' => esc_html__('No', 'pairwise-battler'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'base_session',
            [
                'label' => esc_html__('Base Session Name', 'pairwise-battler'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'pairwise-battler',
                'description' => esc_html__('Used for grouping results. Will be appended with timestamp.', 'pairwise-battler'),
            ]
        );
        
        $this->add_control(
            'webhook_url',
            [
                'label' => esc_html__('Webhook URL (Optional)', 'pairwise-battler'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__('https://example.com/webhook', 'pairwise-battler'),
                'description' => esc_html__('Send data to external service (advanced).', 'pairwise-battler'),
            ]
        );
        
        $this->end_controls_section();
        
        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => esc_html__('Style', 'pairwise-battler'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'heading_color',
            [
                'label' => esc_html__('Heading Color', 'pairwise-battler'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cb-heading' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'heading_typography',
                'label' => esc_html__('Heading Typography', 'pairwise-battler'),
                'selector' => '{{WRAPPER}} .cb-heading',
            ]
        );
        
        $this->add_control(
            'card_border_radius',
            [
                'label' => esc_html__('Card Border Radius', 'pairwise-battler'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 12,
                ],
                'selectors' => [
                    '{{WRAPPER}} .cb-card' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_control(
            'button_bg_color',
            [
                'label' => esc_html__('Button Background', 'pairwise-battler'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cb-reset' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'button_text_color',
            [
                'label' => esc_html__('Button Text Color', 'pairwise-battler'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cb-reset' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_section();
    }
    
    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_id = 'cb-' . $this->get_id();
        
        // Generate shuffle and progress values
        $shuffle = $settings['shuffle'] === 'yes' ? '1' : '0';
        $show_progress = $settings['show_progress'] === 'yes' ? '1' : '0';
        $base_session = !empty($settings['base_session']) ? esc_attr($settings['base_session']) : 'pairwise-battler';
        $webhook = !empty($settings['webhook_url']) ? esc_url($settings['webhook_url']) : '';
        
        // Get REST API URL for use in JavaScript
        $rest_url = rest_url('pairwise-battler/v1/save-results');
        
        ?>
        <div class="cb-widget" id="<?php echo esc_attr($widget_id); ?>"
             data-session="<?php echo $base_session; ?>"
             data-shuffle="<?php echo $shuffle; ?>"
             data-progress="<?php echo $show_progress; ?>"
             data-webhook="<?php echo $webhook; ?>"
             data-rest-url="<?php echo esc_url($rest_url); ?>">
            
            <h3 class="cb-heading"><?php echo esc_html($settings['heading_text']); ?></h3>
            
            <div class="cb-images" style="display:none;">
                <?php foreach ($settings['images_list'] as $item): ?>
                    <figure data-title="<?php echo esc_attr($item['image_title']); ?>">
                        <img src="<?php echo esc_url($item['image']['url']); ?>" 
                             alt="<?php echo esc_attr($item['image_title']); ?>">
                    </figure>
                <?php endforeach; ?>
            </div>
            
            <div class="cb-stage" aria-live="polite"></div>
            
            <div class="cb-progress" hidden>
                <div class="cb-progress-bar" style="width:0%"></div>
            </div>
            
            <div class="cb-footer">
                <button class="cb-reset" type="button" style="display:none;">
                    <?php echo esc_html($settings['reset_button_text']); ?>
                </button>
            </div>
        </div>
        
        <style>
        .cb-widget{ max-width:980px; margin:0 auto; text-align:center; }
        .cb-heading{ margin:0 0 12px; font-weight:700; }
        .cb-row{ display:flex; gap:16px; align-items:stretch; justify-content:center; flex-wrap:wrap; }
        .cb-card{ flex:1 1 380px; border:0; background:#fff; cursor:pointer; padding:0;
                  box-shadow:0 2px 10px rgba(0,0,0,.08); border-radius:12px; overflow:hidden;
                  transition:transform .08s ease, box-shadow .2s ease; }
        .cb-card:hover{ transform:translateY(-2px); box-shadow:0 6px 24px rgba(0,0,0,.12); }
        .cb-card img{ width:100%; height:auto; display:block; }
        .cb-card figcaption{ padding:10px 12px; font-size:14px; opacity:.8; }
        .cb-progress{ height:8px; background:#eee; border-radius:999px; overflow:hidden; margin:14px 0; }
        .cb-progress-bar{ height:8px; width:0%; background:linear-gradient(90deg,#9ae6b4,#38b2ac); }
        .cb-footer{ margin-top:8px; display:flex; gap:8px; justify-content:center; flex-wrap:wrap; }
        .cb-footer button{ background:#f3f4f6; border:1px solid #e5e7eb; border-radius:8px; padding:8px 12px; cursor:pointer; }
        .cb-complete{ padding:24px; background:#f9fafb; border-radius:12px; }
        .cb-result-img{ width:80px; height:80px; object-fit:cover; border-radius:8px; margin-right:12px; vertical-align:middle; }
        .cb-result-row{ display:flex; align-items:center; margin-bottom:8px; }
        @media (prefers-reduced-motion: reduce){ .cb-card{ transition:none; } }



/* --- Mobile-friendly results table --- */
@media (max-width: 640px) {
   /* kill theme zebra/gray backgrounds globally */
.cb-complete table tr,
.cb-complete table td,
.cb-complete table th {
  background: transparent !important;
}
  /* hide table header */
  .cb-complete thead { display:none; }

  /* turn each row into a “card” */
  .cb-complete table,
  .cb-complete tbody,
  .cb-complete tr,
  .cb-complete td { display:block; width:100%; }

  .cb-complete tbody tr {
    background:#fff !important;          /* keep the card white */
    border:1px solid #e5e7eb;
    border-radius:12px;
    margin:12px 0;
    padding:12px;
    box-shadow:0 1px 2px rgba(0,0,0,.04);
  }

  .cb-complete tbody td { 
    border:none; 
    padding:8px 0; 
    background:transparent !important;    /* remove inner gray cells */
  }

  /* labels above each value, taken from data-label="" */
  .cb-complete tbody td::before {
    content: attr(data-label);
    display:block;
    font-size:12px;
    font-weight:600;
    color:#6b7280;
    margin-bottom:6px;
  }

  /* first cell keeps the image+title layout */
  .cb-complete tbody td[data-label="Image"] {
    display:flex;
    gap:12px;
    align-items:flex-start;
  }
  /* hide the "Image" label text on mobile */
  .cb-complete tbody td[data-label="Image"]::before {
    content:"" !important;
    display:none !important;
  }

  /* numbers: label left, value right */
  .cb-complete tbody td[data-label="Clicks"],
  .cb-complete tbody td[data-label="Click %"] {
    display:flex;
    justify-content:space-between;
    align-items:center;
    text-align:left !important; /* overrides inline right-align */
  }

  /* image size a tad smaller on mobile */
  .cb-result-img { width:64px; height:64px; }
}


        </style>
        
        <?php
        // Read and include the JavaScript
        $this->render_javascript($widget_id, $settings['completion_text']);
    }
    
    protected function render_javascript($widget_id, $completion_text) {
        ?>
        <script>
        (function() {
          const root = document.getElementById('<?php echo esc_js($widget_id); ?>');
          if (!root) return;
          
          init(root);
          
          async function init(root){
            const cfg = await readConfig(root);
            const images = readImages(root);
            if (images.length < 2){
              root.querySelector('.cb-stage').innerHTML =
                '<em>Add at least 2 photos to start the comparison.</em>';
              return;
            }

            let state = loadState(cfg) || startState(images.length, !!cfg.shuffle);
            saveState(cfg, state);
            render(root, cfg, images, state);

            root.addEventListener('click', (e) => {
              const card = e.target.closest('.cb-card');
              if (card){
                e.preventDefault();
                const winnerId = parseInt(card.getAttribute('data-choice'),10);
                const [aIdx,bIdx] = state.pairs[state.current];
                const a = images[aIdx], b = images[bIdx];

                state.results.push({
                  image1_id: a.id, image2_id: b.id, winner_id: winnerId,
                  image1_title: a.title, image2_title: b.title,
                  winner_title: (winnerId === a.id ? a.title : b.title),
                  ts: new Date().toISOString()
                });
                state.appearances[a.id] = (state.appearances[a.id]||0) + 1;
                state.appearances[b.id] = (state.appearances[b.id]||0) + 1;
                state.clicks[winnerId] = (state.clicks[winnerId]||0) + 1;

                if (cfg.webhook){
                  try {
                    fetch(cfg.webhook, {
                      method: 'POST',
                      headers: {'Content-Type': 'application/json'},
                      body: JSON.stringify({
                        session: cfg.session,
                        container: cfg.id,
                        page: location.href,
                        image1_id: a.id, image2_id: b.id, winner_id: winnerId,
                        image1_title: a.title, image2_title: b.title,
                        ts: new Date().toISOString()
                      })
                    }).catch(()=>{});
                  } catch(_) {}
                }

                state.current++;
                saveState(cfg, state);
                
                const isTestComplete = state.current >= state.pairs.length;
                if (isTestComplete) {
                  const completeWins = {};
                  let maxClicks = 0;
                  let topImages = [];
                  
                  images.forEach(img => {
                    const clicks = state.clicks[img.id] || 0;
                    if (clicks > maxClicks) {
                      maxClicks = clicks;
                      topImages = [img.id];
                    } else if (clicks === maxClicks) {
                      topImages.push(img.id);
                    }
                  });
                  
                  let winnerId = null;
                  if (topImages.length > 1) {
                    winnerId = resolveHeadToHead(topImages, state.results);
                  } else if (topImages.length === 1) {
                    winnerId = topImages[0];
                  }
                  
                  if (winnerId !== null) {
                    completeWins[winnerId] = 1;
                  }
                  
                  state.completeWins = completeWins;
                  saveState(cfg, state);
                  saveToWordPress(state.results, cfg, images, state);
                }
                
                render(root, cfg, images, state);
              }
            });

            root.querySelector('.cb-reset').addEventListener('click', () => {
              clearState(cfg);
              state = startState(images.length, !!cfg.shuffle);
              saveState(cfg, state);
              render(root, cfg, images, state);
            });
          }

          async function readConfig(root){
            const baseSession = root.getAttribute('data-session') || 'pairwise-battler';
            const userSession = await getUserSession(baseSession);
            
            return {
              id: root.id || 'cb-'+Math.random().toString(36).slice(2,8),
              session: userSession,
              shuffle: root.getAttribute('data-shuffle') === '1',
              progress: root.getAttribute('data-progress') === '1',
              webhook: (root.getAttribute('data-webhook') || '').trim(),
              restUrl: root.getAttribute('data-rest-url') || '/wp-json/pairwise-battler/v1/save-results'
            };
          }
          
          async function getUserSession(baseSession){
            const storageKey = 'CB_USER_SESSION';
            let userSession = localStorage.getItem(storageKey);
            
            if (userSession) {
              return userSession;
            }
            
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const randomId = Math.random().toString(36).substring(2, 8);
            
            userSession = `${year}-${month}-${day}-${hours}-${minutes}-${randomId}`;
            localStorage.setItem(storageKey, userSession);
            
            return userSession;
          }

          function readImages(root){
            const figs = root.querySelectorAll('.cb-images figure');
            const items = [];
            let id = 1;
            figs.forEach(f => {
              const img = f.querySelector('img');
              if (!img) return;
              const url = (img.getAttribute('src')||'').trim();
              if (!url) return;
              const title = f.getAttribute('data-title') || img.getAttribute('alt') || `Image ${id}`;
              items.push({ id:id++, url, title });
            });
            return items;
          }

          function startState(n, doShuffle){
            const pairs = [];
            for (let i=0;i<n;i++){
              for (let j=i+1;j<n;j++) pairs.push([i,j]);
            }
            if (doShuffle) shuffle(pairs);
            return {
              pairs, current: 0, done: [],
              clicks: {}, completeWins: {}, appearances: {}, results: []
            };
          }

          function shuffle(arr){
            for(let i=arr.length-1;i>0;i--){
              const j = Math.floor(Math.random()*(i+1));
              [arr[i],arr[j]]=[arr[j],arr[i]];
            }
            return arr;
          }
          
          function resolveHeadToHead(tiedImages, results){
            const headToHeadWins = {};
            tiedImages.forEach(id => { headToHeadWins[id] = 0; });
            
            results.forEach(result => {
              const img1InTie = tiedImages.includes(result.image1_id);
              const img2InTie = tiedImages.includes(result.image2_id);
              if (img1InTie && img2InTie) {
                if (tiedImages.includes(result.winner_id)) {
                  headToHeadWins[result.winner_id]++;
                }
              }
            });
            
            let maxH2HWins = -1;
            let h2hWinner = null;
            tiedImages.forEach(id => {
              if (headToHeadWins[id] > maxH2HWins) {
                maxH2HWins = headToHeadWins[id];
                h2hWinner = id;
              }
            });
            
            return h2hWinner || tiedImages[0];
          }

          function key(cfg){ return `CB_${cfg.session}_${cfg.id}`; }
          function loadState(cfg){
            try{ const raw = localStorage.getItem(key(cfg)); return raw ? JSON.parse(raw) : null; }
            catch(_){ return null; }
          }
          function saveState(cfg, state){
            try{ localStorage.setItem(key(cfg), JSON.stringify(state)); } catch(_){}
          }
          function clearState(cfg){
            try{ localStorage.removeItem(key(cfg)); } catch(_){}
          }

          function render(root, cfg, images, state){
            const stage = root.querySelector('.cb-stage');
            const progWrap = root.querySelector('.cb-progress');
            const bar = root.querySelector('.cb-progress-bar');
            const resetBtn = root.querySelector('.cb-reset');
            const total = state.pairs.length;
            const done = Math.min(state.current, total);
            const pct = total ? Math.round((done/total)*100) : 0;

            if (cfg.progress){
              progWrap.hidden = false;
              bar.style.width = pct + '%';
            } else {
              progWrap.hidden = true;
            }

            if (done >= total){
              stage.innerHTML = renderComplete(images, state);
              if (resetBtn) resetBtn.style.display = 'inline-block';
              return;
            }

            if (resetBtn) resetBtn.style.display = 'none';

            const [aIdx,bIdx] = state.pairs[state.current];
            const a = images[aIdx], b = images[bIdx];
            stage.innerHTML = `
              <div class="cb-round" style="margin-bottom:8px;opacity:.7;">Round ${done+1} of ${total}</div>
              <div class="cb-row">
                ${card(a)}
                ${card(b)}
              </div>
            `;
          }

          function esc(s){ return String(s).replace(/[&<>"]/g, r=>({"&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;"}[r])); }
          function card(img){
            return `
              <button class="cb-card" data-choice="${img.id}" aria-label="Vote for ${esc(img.title)}" type="button">
                <figure>
                  <img src="${esc(img.url)}" alt="${esc(img.title)}" loading="lazy">
                  <figcaption>${esc(img.title)}</figcaption>
                </figure>
              </button>
            `;
          }

          function renderComplete(images, state){
            const rows = images.map(img => {
              const app = state.appearances[img.id] || 0;
              const clicks = state.clicks[img.id] || 0;
              const completeWins = state.completeWins[img.id] || 0;
              const pct = app ? Math.round((clicks/app)*1000)/10 : 0;
              return { 
                id: img.id, 
                title: img.title, 
                url: img.url,
                clicks: clicks, 
                completeWins: completeWins, 
                appearances: app, 
                pct: pct 
              };
            }).sort((a,b) => b.clicks - a.clicks || b.pct - a.pct);

            const items = rows.map(r => `
              <tr>
                <td data-label="Image" style="display:flex; align-items:center;">
                  <img src="${esc(r.url)}" alt="${esc(r.title)}" class="cb-result-img">
                  <div>
                    <strong>${esc(r.title)}</strong>
                    ${r.completeWins > 0 ? '<br><span style="color:#28a745; font-size:12px;">★ Winner</span>' : ''}
                  </div>
                </td>
                <td data-label="Clicks" style="text-align:right;">${r.clicks}</td>
                <td data-label="Click %" style="text-align:right;">${r.pct}%</td>
              </tr>
            `).join('');

            return `
              <div class="cb-complete">
                <p><strong><?php echo esc_js($completion_text); ?></strong></p>
                <table class="cb-results" style="width:100%; border-collapse:collapse;">
                  <thead>
                    <tr>
                      <th style="text-align:left;">Image</th>
                      <th style="text-align:right;">Clicks</th>
                      <th style="text-align:right;">Click %</th>
                    </tr>
                  </thead>
                  <tbody>${items || '<tr><td colspan="4">No results.</td></tr>'}</tbody>
                </table>
              </div>
            `;

          }

          function saveToWordPress(results, cfg, images, state){
            const summaryData = images.map(img => ({
              id: img.id,
              title: img.title,
              clicks: state.clicks[img.id] || 0,
              completeWins: state.completeWins[img.id] || 0,
              appearances: state.appearances[img.id] || 0
            }));

            fetch(cfg.restUrl, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({
                session: cfg.session,
                container_id: cfg.id,
                timestamp: new Date().toISOString(),
                results: results,
                summary: summaryData
              })
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                console.log('Results saved successfully to database!');
              } else {
                console.error('Error saving results:', data.message || 'Unknown error');
              }
            })
            .catch(error => {
              console.error('Error saving to WordPress:', error);
            });
          }
        })();
        </script>
        <?php
    }
}
