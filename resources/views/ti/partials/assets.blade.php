@once
{{-- Embedded deliberately: cPanel may serve public assets from a separate document root. No new CSS/JS URL is required. --}}
<style id="timcare-ti-styles-v2">
.tc { --tc-bg:#fff; --tc-ink:#18243a; --tc-muted:#536177; --tc-line:#d4dce8; --tc-soft:#f4f7fb; --tc-accent:#364fc7; color:var(--tc-ink); padding:28px; max-width:1500px; margin:auto; font-size:15px; line-height:1.55; }
.dark .tc { --tc-bg:#1d2939; --tc-ink:#edf2f9; --tc-muted:#b7c3d5; --tc-line:#4b5d73; --tc-soft:#243449; --tc-accent:#a5b9ff; }
.tc * { box-sizing:border-box; } .tc h1 { font-size:28px; font-weight:700; line-height:1.25; margin:0 0 8px; } .tc h2 { font-size:19px; font-weight:650; margin:0 0 16px; }
.tc p { margin:0 0 12px; } .tc-muted,.tc small { color:var(--tc-muted); } .tc small { font-size:13px; }
.tc-header { margin-bottom:24px; } .tc-nav { display:flex; gap:8px; flex-wrap:wrap; border-bottom:1px solid var(--tc-line); padding:12px 0; margin-bottom:24px; }
.tc-nav a { color:var(--tc-muted); padding:9px 14px; border-radius:6px; font-weight:600; } .tc-nav a[aria-current=page] { background:var(--tc-soft); color:var(--tc-accent); box-shadow:inset 0 -3px var(--tc-accent); }
.tc-toolbar { display:flex; gap:12px; align-items:center; justify-content:space-between; flex-wrap:wrap; margin:0 0 20px; }
.tc-panel { background:var(--tc-bg); border:1px solid var(--tc-line); border-radius:10px; padding:24px; margin-bottom:20px; }
.tc-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:20px; } .tc-wide { grid-column:1/-1; }
.tc-field label,.tc legend { display:block; font-weight:600; margin-bottom:7px; } .tc-field small { display:block; margin-top:6px; }
.tc input:not([type=checkbox]),.tc textarea,.tc select { width:100%; border:1px solid var(--tc-line); border-radius:6px; padding:10px 12px; min-height:44px; background:var(--tc-bg); color:var(--tc-ink); font:inherit; }
.tc textarea { min-height:105px; resize:vertical; } .tc input[type=checkbox] { width:18px; height:18px; flex-shrink:0; accent-color:#364fc7; }
.tc input:focus,.tc select:focus,.tc textarea:focus,.tc a:focus-visible,.tc button:focus-visible { outline:3px solid var(--tc-accent); outline-offset:3px; }
.tc-btn { display:inline-flex; align-items:center; justify-content:center; min-height:44px; padding:10px 16px; border:1px solid var(--tc-line); border-radius:6px; background:var(--tc-bg); color:var(--tc-ink); font-weight:600; cursor:pointer; gap:6px; text-decoration:none; }
.tc-btn-primary { background:#364fc7; border-color:#364fc7; color:white; } .tc-btn:hover { filter:brightness(.94); } .tc-actions { display:flex; flex-wrap:wrap; gap:10px; align-items:center; }
.tc-notice { padding:14px 18px; background:var(--tc-soft); border-left:4px solid var(--tc-accent); margin-bottom:20px; border-radius:4px; }
.tc-success { border-left-color:#15803d; } .tc-error { color:#b42318; font-size:14px; margin-top:6px; } .dark .tc-error { color:#ffb4ab; } .tc-errors { border-left-color:#b42318; }
.tc-table-wrap { overflow-x:auto; background:var(--tc-bg); border:1px solid var(--tc-line); border-radius:8px; margin-bottom:20px; }
.tc table { width:100%; border-collapse:collapse; text-align:left; } .tc th { background:var(--tc-soft); font-size:13px; font-weight:650; white-space:nowrap; }
.tc th,.tc td { padding:14px 16px; border-bottom:1px solid var(--tc-line); vertical-align:top; } .tc tbody tr:last-child td { border-bottom:0; } .tc td { min-width:110px; } .tc td strong { display:block; } .tc td small { display:block; }
.tc-link { color:var(--tc-accent); text-decoration:underline; text-underline-offset:3px; font-weight:600; } .tc-chip { display:inline-block; padding:3px 9px; border:1px solid var(--tc-line); border-radius:5px; font-size:12px; margin:2px 3px 2px 0; background:var(--tc-soft); }
.tc-checks { display:grid; gap:7px; max-height:280px; overflow:auto; padding:12px; border:1px solid var(--tc-line); border-radius:6px; background:var(--tc-bg); }
.tc-check { display:flex; gap:10px; align-items:flex-start; padding:7px 3px; font-weight:400 !important; margin:0 !important; cursor:pointer; }
.tc-filters { display:grid; grid-template-columns:2fr 1.4fr 1fr 1fr auto; gap:12px; align-items:end; } .tc dd { white-space:pre-wrap; overflow-wrap:anywhere; margin:0; } .tc dt { font-weight:600; margin-bottom:5px; }
.tc-readonly { color:var(--tc-muted); background:var(--tc-soft); padding:12px; border-radius:6px; } .tc-empty { text-align:center; padding:42px 22px; } .tc-empty h2 { margin-bottom:8px; }
@media(max-width:900px) { .tc-filters { grid-template-columns:repeat(2,minmax(0,1fr)); } }
@media(max-width:640px) { .tc { padding:18px 14px; } .tc h1 { font-size:24px; } .tc-panel { padding:18px 15px; } .tc-grid,.tc-filters { grid-template-columns:1fr; } .tc-nav a { padding:9px; } .tc-toolbar { align-items:flex-start; } .tc-actions .tc-btn { flex-grow:1; } }
@media print { .tc-nav,.tc-actions,.tc-toolbar form { display:none; } .tc-panel { break-inside:avoid; } }

.tc-start { border-top:4px solid var(--tc-accent); }
.tc-form-head { display:flex; align-items:start; justify-content:space-between; gap:16px; flex-wrap:wrap; }
.tc-subtitle { max-width:78ch; }
.tc-field [aria-invalid="true"] { border-color:#b42318; }
.tc-field .tc-selected-value { margin-top:7px; overflow-wrap:anywhere; }
.tc-help { color:var(--tc-muted); font-size:13px; margin:6px 0 0; }
.tc-errors a { text-decoration:underline; color:inherit; }
.tc [aria-busy="true"] button[type="submit"] { opacity:.7; cursor:wait; }
.tc button:disabled { opacity:.65; cursor:not-allowed; }
.tc .tc-field { min-width:0; }
.tc select { text-overflow:ellipsis; }
.tc .tc-start-form { display:grid; grid-template-columns:2fr 1.5fr auto; gap:16px; align-items:end; }
.tc .tc-search-form { display:grid; grid-template-columns:minmax(160px,340px) auto; align-items:end; gap:12px; }
.tc .tc-search-form .tc-field { margin:0; }
.tc .tc-section-caption { margin-bottom:20px; color:var(--tc-muted); max-width:80ch; }
.tc .tc-btn { font-family:inherit; }
.tc .tc-errors ul { list-style:disc; padding-left:22px; margin-top:8px; }
.tc .tc-summary-focus:focus { outline:3px solid var(--tc-accent); outline-offset:3px; }
@media(max-width:850px) { .tc .tc-start-form { grid-template-columns:1fr; } }
@media(max-width:480px) { .tc .tc-search-form { grid-template-columns:1fr; width:100%; } }

.tc { --tc-accent:#3156c8; padding-top:32px; }
.dark .tc { --tc-accent:#a5b9ff; }
.tc-header { padding:0 2px 6px; }
.tc h1 { letter-spacing:-.025em; }
.tc h2 { letter-spacing:-.015em; }
.tc-nav { gap:6px; border:0; padding:4px; margin:-8px 0 24px; border-radius:10px; background:var(--tc-soft); width:fit-content; max-width:100%; }
.tc-nav a { border-radius:7px; text-decoration:none; transition:background-color .15s ease,color .15s ease; }
.tc-nav a:hover { background:var(--tc-bg); color:var(--tc-ink); }
.tc-nav a[aria-current=page] { background:var(--tc-bg); color:var(--tc-accent); box-shadow:0 1px 3px #18243a1c; }
.tc-panel { border-radius:14px; box-shadow:0 3px 14px #18243a08; }
.dark .tc-panel { box-shadow:0 3px 14px #00000014; }
.tc-panel-heading { display:flex; align-items:end; justify-content:space-between; gap:18px; margin-bottom:22px; }
.tc-panel-heading h2 { margin:3px 0 0; }
.tc-panel-heading .tc-section-caption { margin:0; }
.tc-eyebrow { color:var(--tc-accent); font-size:11px; font-weight:750; letter-spacing:.12em; }
.tc-section-caption { max-width:60ch; }
.tc-field label,.tc legend { font-size:14px; }
.tc input:not([type=checkbox]),.tc textarea,.tc select { border-radius:8px; transition:border-color .15s ease,box-shadow .15s ease; }
.tc input:focus,.tc select:focus,.tc textarea:focus { box-shadow:0 0 0 3px color-mix(in srgb,var(--tc-accent) 15%,transparent); }
.tc-btn { border-radius:8px; transition:transform .15s ease,box-shadow .15s ease,background-color .15s ease; }
.tc-btn:hover { transform:translateY(-1px); box-shadow:0 3px 9px #18243a16; }
.tc-btn-primary { background:var(--tc-accent); border-color:var(--tc-accent); }
.tc .tc-start-form { grid-template-columns:minmax(240px,.8fr) minmax(0,2fr); align-items:start; }
.tc-report-types { min-width:0; margin:0; padding:0; border:0; }
.tc-report-types legend { margin-bottom:8px; }
.tc-report-type-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:10px; }
.tc-report-type { display:flex; align-items:center; gap:10px; min-height:82px; padding:13px; border:1px solid var(--tc-line); border-radius:10px; background:var(--tc-bg); color:var(--tc-ink); text-align:left; font:inherit; cursor:pointer; transition:border-color .15s ease,background-color .15s ease,transform .15s ease; }
.tc-report-type:hover { border-color:var(--tc-accent); background:var(--tc-soft); transform:translateY(-2px); }
.tc-report-type:focus-visible { outline:3px solid var(--tc-accent); outline-offset:3px; }
.tc-report-type-icon { display:grid; width:34px; height:34px; flex:0 0 34px; place-items:center; border-radius:9px; background:var(--tc-soft); color:var(--tc-accent); font-size:12px; font-weight:750; }
.tc-report-type strong,.tc-report-type small { display:block; }
.tc-report-type strong { font-size:13px; }
.tc-report-type small { margin-top:3px; font-size:11px; line-height:1.35; }
.tc-report-type-arrow { margin-left:auto; color:var(--tc-accent); font-size:19px; }
.tc-panel-footer { margin-top:18px; padding-top:14px; border-top:1px solid var(--tc-line); }
.tc-filter-actions { display:flex; align-items:center; gap:14px; }
.tc-filters { grid-template-columns:minmax(180px,1.3fr) minmax(180px,1.5fr) minmax(150px,1fr) minmax(100px,.7fr) minmax(130px,.8fr) auto; }
.tc-results-heading { display:flex; align-items:baseline; gap:12px; margin:28px 2px 12px; }
.tc-results-heading h2 { margin:0; }
.tc table tbody tr { transition:background-color .12s ease; }
.tc table tbody tr:hover { background:color-mix(in srgb,var(--tc-soft) 64%,transparent); }
.tc-empty-mark { display:grid; width:42px; height:42px; place-items:center; margin:0 auto 12px; border-radius:50%; background:var(--tc-soft); color:var(--tc-muted); font-weight:700; }
.tc-form-context { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:16px 20px; margin-bottom:18px; border:1px solid var(--tc-line); border-radius:12px; background:var(--tc-soft); }
.tc-form-context strong,.tc-form-context small { display:block; }
.tc-form-context strong { margin-top:4px; font-size:17px; }
.tc-form-context small { margin-top:2px; }
.tc-report-back { display:inline-flex; margin:0 0 12px 2px; color:var(--tc-accent); font-size:13px; font-weight:650; text-decoration:none; }
.tc-report-back:hover { text-decoration:underline; text-underline-offset:3px; }
.tc-form-context { align-items:stretch; gap:0; padding:0; overflow:hidden; }
.tc-form-context-object,.tc-form-context-type { min-width:0; flex:1; padding:16px 20px; }
.tc-form-context-object { flex:1.6; }
.tc-form-context-type { border-left:1px solid var(--tc-line); }
.tc-form-context small { overflow-wrap:anywhere; }
.tc-form-section { padding:22px 24px; }
.tc-form-section-heading { display:flex; align-items:flex-start; gap:12px; margin-bottom:18px; }
.tc-form-section-heading h2 { margin:0 0 3px; }
.tc-form-section-heading p { margin:0; font-size:13px; }
.tc-step { display:grid; width:32px; height:32px; flex:0 0 32px; place-items:center; border:1px solid var(--tc-line); border-radius:10px; background:var(--tc-soft); color:var(--tc-accent); font-size:13px; font-weight:750; }
.tc-period-fields { display:grid; grid-template-columns:repeat(2,minmax(0,240px)); gap:14px; }
.tc-period-fields .tc-field { margin:0; }
.tc-period-summary { display:inline-flex; align-items:center; gap:9px; padding:9px 13px; border:1px solid var(--tc-line); border-radius:9px; background:var(--tc-soft); }
.tc-period-summary span { color:var(--tc-muted); }
.tc-period-summary strong { color:var(--tc-ink); }
.tc-existing-report { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:14px 16px; margin-bottom:16px; border:1px solid #f3d28b; border-radius:10px; background:#fffbeb; color:#713f12; }
.tc-existing-report strong { display:block; }
.tc-existing-report p { margin:3px 0 0; font-size:13px; }
.tc-existing-report .tc-btn { flex:0 0 auto; border-color:#f3d28b; color:#713f12; }
.dark .tc-existing-report { border-color:#785c2b; background:#312919; color:#f8dfaa; }
.dark .tc-existing-report .tc-btn { border-color:#785c2b; color:#f8dfaa; }
.tc-required-mark { color:var(--tc-accent); font-weight:750; }
.tc-form-guidance { display:flex; align-items:flex-start; gap:10px; padding:12px 14px; margin:0 0 18px; border-left:3px solid var(--tc-accent); border-radius:0 9px 9px 0; background:var(--tc-soft); font-size:13px; }
.tc-form-guidance strong { flex:0 0 auto; }
.tc-form-guidance span { color:var(--tc-muted); }
.tc-report-fields { row-gap:18px; }
.tc-form-actions { position:sticky; bottom:0; z-index:5; display:flex; align-items:center; justify-content:space-between; gap:16px; padding:14px 18px; margin:16px -2px 0; border:1px solid var(--tc-line); border-radius:12px; background:var(--tc-bg); box-shadow:0 -4px 16px #18243a0d; }
.tc-form-actions-note { display:grid; gap:2px; color:var(--tc-muted); font-size:13px; }
.tc-form-actions-note strong { color:var(--tc-ink); font-size:13px; }
.tc-form-actions-note small { font-size:12px; }
.tc-table-wrap { border-radius:12px; }
.tc-start-empty { display:flex; align-items:flex-start; gap:16px; padding:18px; border:1px solid var(--tc-line); border-radius:12px; background:var(--tc-soft); }
.tc-start-empty .tc-empty-mark { flex:0 0 42px; margin:0; }
.tc-start-empty h3 { margin:0 0 5px; font-size:16px; }
.tc-start-empty p { max-width:72ch; margin:0 0 12px; color:var(--tc-muted); }
.tc-start-empty .tc-btn { min-height:40px; }
.tc-report-filters { grid-template-columns:repeat(3,minmax(0,1fr)); gap:14px; align-items:end; padding:18px; border:1px solid var(--tc-line); border-radius:12px; background:var(--tc-soft); }
.tc-report-filters .tc-field { margin:0; }
.tc-report-filters .tc-field label { font-size:12px; }
.tc-report-filters input:not([type=checkbox]),.tc-report-filters select { min-height:42px; padding:8px 10px; font-size:13px; }
.tc-report-search { grid-column:span 2; }
.tc-filter-actions { display:flex; grid-column:1/-1; align-items:center; justify-content:flex-end; gap:8px; }
.tc-filter-actions .tc-btn { min-height:42px; padding:8px 12px; white-space:nowrap; }
.tc-btn-reset { color:var(--tc-muted); }
.tc-report-export { display:flex; align-items:center; justify-content:space-between; gap:16px; padding-top:16px; margin-top:16px; border-top:1px solid var(--tc-line); }
.tc-report-export p { margin:0; color:var(--tc-muted); font-size:13px; }
.tc-report-export .tc-btn { flex:0 0 auto; }
.tc-results-heading { justify-content:space-between; }
.tc-report-table-wrap { margin-top:0; }
.tc-report-table { min-width:850px; }
.tc-report-table th { padding-top:12px; padding-bottom:12px; }
.tc-report-table td { min-width:0; }
.tc-report-table td:first-child { min-width:190px; }
.tc-report-table td:nth-child(2) { min-width:190px; }
.tc-report-row-action { min-height:36px; padding:6px 10px; font-size:13px; }
.tc-report-table .tc-empty { min-width:0; }
@media(max-width:700px) {
    .tc-report-table { min-width:0; display:block; }
    .tc-report-table thead { position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border:0; }
    .tc-report-table tbody { display:grid; gap:12px; padding:12px; }
    .tc-report-table tbody tr { display:block; overflow:hidden; border:1px solid var(--tc-line); border-radius:12px; background:var(--tc-bg); }
    .tc-report-table tbody td { display:grid; grid-template-columns:minmax(104px,.75fr) minmax(0,1.25fr); gap:12px; align-items:start; padding:12px; border-bottom:1px solid var(--tc-line); }
    .tc-report-table tbody td::before { content:attr(data-label); color:var(--tc-muted); font-size:11px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
    .tc-report-table tbody td[colspan] { display:block; }
    .tc-report-table tbody td[colspan]::before { content:none; }
    .tc-report-table .tc-actions { gap:6px; }
}
@media(max-width:1000px) { .tc-report-history .tc-report-filters { grid-template-columns:repeat(2,minmax(0,1fr)); } .tc-report-search { grid-column:span 2; } .tc-filter-actions { grid-column:span 2; } }
@media(max-width:700px) { .tc-start-empty { padding:14px; } .tc-report-history .tc-report-filters { grid-template-columns:repeat(2,minmax(0,1fr)); padding:14px; } .tc-report-search { grid-column:span 2; } .tc-filter-actions { grid-column:span 2; flex-wrap:wrap; } .tc-report-export { align-items:flex-start; flex-direction:column; } .tc-results-heading { align-items:flex-start; flex-direction:column; gap:2px; } }
@media(max-width:480px) { .tc-report-history .tc-report-filters { grid-template-columns:1fr; } .tc-report-search,.tc-filter-actions { grid-column:auto; } .tc-filter-actions .tc-btn { flex:1; } .tc-start-empty { flex-direction:column; } }
@media(max-width:1000px) { .tc .tc-start-form { grid-template-columns:1fr; } .tc-report-type-grid { grid-template-columns:repeat(3,minmax(0,1fr)); } .tc-filters { grid-template-columns:repeat(3,minmax(0,1fr)); } }
@media(max-width:700px) { .tc { padding-top:22px; } .tc-nav { width:100%; } .tc-nav a { flex:1 1 auto; text-align:center; } .tc-panel-heading { align-items:flex-start; flex-direction:column; gap:5px; } .tc-report-type-grid { grid-template-columns:1fr; } .tc-report-type { min-height:68px; } .tc-form-context { align-items:flex-start; flex-direction:column; } .tc-form-actions { align-items:stretch; flex-direction:column; padding:12px; } .tc-form-actions .tc-actions { width:100%; } .tc-form-actions .tc-btn { flex:1; } .tc-form-actions-note { font-size:12px; } .tc-filters { grid-template-columns:1fr; } .tc-filter-actions { justify-content:space-between; } }
@media(max-width:700px) { .tc-form-context-object,.tc-form-context-type { width:100%; padding:14px 16px; } .tc-form-context-type { border-top:1px solid var(--tc-line); border-left:0; } .tc-period-fields { grid-template-columns:1fr; } .tc-existing-report { align-items:stretch; flex-direction:column; } .tc-existing-report .tc-btn { width:100%; } .tc-form-guidance { flex-direction:column; gap:3px; } .tc-form-actions-note { align-self:flex-start; } }
@media(prefers-reduced-motion:reduce) { .tc *, .tc *::before, .tc *::after { scroll-behavior:auto !important; transition-duration:.01ms !important; animation-duration:.01ms !important; } }
.tc-team-panel { padding:22px; }
.tc-team-heading { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:18px; }
.tc-team-heading h2 { margin:0 0 3px; }
.tc-team-heading p { margin:0; font-size:13px; }
.tc-team-table-wrap { margin-bottom:0; }
.tc-team-table th { padding-top:11px; padding-bottom:11px; }
.tc-team-table td { min-width:0; padding:10px 12px; vertical-align:middle; }
.tc-team-table td:first-child { width:34%; min-width:210px; }
.tc-team-table td:nth-child(2) { width:48%; min-width:260px; }
.tc-team-table .tc-field { margin:0; }
.tc-team-table .tc-field small { margin-top:3px; }
.tc-team-table input:not([type=checkbox]),.tc-team-table select { min-height:40px; padding:8px 10px; }
.tc-team-table .tc-field label:not(.tc-visually-hidden) { position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border:0; }
.tc-visually-hidden { position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border:0; }
.tc-team-count { display:inline-grid; min-width:30px; min-height:30px; place-items:center; border-radius:8px; background:var(--tc-soft); font-size:13px; font-weight:650; }
.tc-add-team { margin-top:18px; border-top:1px solid var(--tc-line); padding-top:15px; }
.tc-add-team summary { width:fit-content; color:var(--tc-accent); font-weight:650; cursor:pointer; }
.tc-add-team-fields { margin-top:16px; }
.tc-team-save { position:sticky; bottom:0; z-index:5; display:flex; align-items:center; justify-content:space-between; gap:16px; padding:14px 0 0; margin-top:18px; border-top:1px solid var(--tc-line); background:var(--tc-bg); }
@media(max-width:640px) { .tc-team-panel { padding:16px 12px; } .tc-team-heading { align-items:flex-start; flex-direction:column; } .tc-team-table td:first-child { min-width:180px; } .tc-team-table td:nth-child(2) { min-width:230px; } .tc-team-save { align-items:stretch; flex-direction:column; } .tc-team-save .tc-btn { width:100%; } }
.tc-team-table td:last-child { white-space:nowrap; }
.tc-members-panel { max-width:900px; margin-right:auto; margin-left:auto; }
.tc-member-search { max-width:440px; margin-bottom:16px; }
.tc-member-list { display:grid; gap:8px; max-height:min(58vh,620px); overflow:auto; margin:0; padding:8px; border:1px solid var(--tc-line); border-radius:12px; }
.tc-member-option { display:flex; align-items:center; gap:12px; min-width:0; padding:11px 12px; border:1px solid transparent; border-radius:9px; background:var(--tc-bg); cursor:pointer; }
.tc-member-option:hover { border-color:var(--tc-line); background:var(--tc-soft); }
.tc-member-option:has(input:checked) { border-color:color-mix(in srgb,var(--tc-accent) 32%,var(--tc-line)); background:color-mix(in srgb,var(--tc-accent) 6%,var(--tc-bg)); }
.tc-member-option input[type=checkbox] { width:19px; height:19px; flex:0 0 19px; accent-color:var(--tc-accent); }
.tc-member-option input:disabled { opacity:.65; }
.tc-member-details { display:block; min-width:0; flex:1; }
.tc-member-details strong,.tc-member-details small { display:block; overflow:hidden; text-overflow:ellipsis; }
.tc-member-details small { margin-top:2px; }
.tc-member-no-results { padding:18px; margin:0; color:var(--tc-muted); text-align:center; }
.tc-team-save .tc-actions { justify-content:flex-end; }
@media(max-width:640px) { .tc-member-option { align-items:flex-start; padding:10px 8px; } .tc-member-option .tc-chip { margin-left:auto; } .tc-team-save .tc-actions { flex-direction:column-reverse; } }
.tc-object-toolbar { display:flex; align-items:flex-end; justify-content:space-between; gap:16px; margin-bottom:18px; }
.tc-object-add { flex-shrink:0; }
.tc-object-summary { display:flex; gap:10px; margin-bottom:18px; }
.tc-object-summary > div,.tc-object-summary > a { display:flex; align-items:center; gap:9px; padding:9px 13px; border:1px solid var(--tc-line); border-radius:10px; background:var(--tc-bg); text-decoration:none; }
.tc-object-summary > a:hover { border-color:var(--tc-accent); }
.tc-object-summary-value { color:var(--tc-ink); font-size:17px; font-weight:750; }
.tc-object-summary-label { color:var(--tc-muted); font-size:12px; }
.tc-object-list { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; align-items:stretch; }
.tc-object-card { display:flex; flex-direction:column; gap:14px; margin:0; padding:18px; }
.tc-object-card-heading { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; }
.tc-object-title { min-width:0; }
.tc-object-title h2 { margin:3px 0 0; overflow-wrap:anywhere; }
.tc-object-badges { display:flex; flex-wrap:wrap; justify-content:flex-end; gap:5px; }
.tc-chip-active { border-color:#bbf7d0; background:#f0fdf4; color:#166534; }
.tc-chip-inactive { border-color:var(--tc-line); color:var(--tc-muted); }
.tc-chip-warning { border-color:#fed7aa; background:#fff7ed; color:#9a3412; }
.dark .tc-chip-active { border-color:#166534; background:#142c21; color:#86efac; }
.dark .tc-chip-warning { border-color:#9a3412; background:#351f12; color:#fdba74; }
.tc-object-meta { display:flex; flex-wrap:wrap; gap:7px; color:var(--tc-muted); font-size:13px; }
.tc-object-assignments { display:grid; grid-template-columns:minmax(110px,.75fr) minmax(0,1.5fr); gap:12px; padding:12px 0; border-top:1px solid var(--tc-line); border-bottom:1px solid var(--tc-line); }
.tc-object-assignment { min-width:0; }
.tc-object-assignment > strong { display:block; margin-top:3px; font-size:14px; overflow-wrap:anywhere; }
.tc-object-label { color:var(--tc-muted); font-size:11px; font-weight:700; letter-spacing:.07em; text-transform:uppercase; }
.tc-object-teams { display:grid; gap:6px; margin-top:3px; }
.tc-object-team { font-size:13px; font-weight:650; overflow-wrap:anywhere; }
.tc-object-team small { display:block; font-size:11px; font-weight:400; }
.tc-object-card-footer { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px; margin-top:auto; }
.tc-object-report-count { color:var(--tc-muted); font-size:12px; }
.tc-object-card-footer .tc-actions { gap:7px; }
.tc-object-card-footer .tc-btn { min-height:38px; padding:7px 10px; font-size:13px; }
.dark .tc-chip-active,.dark .tc-chip-warning { background:var(--tc-soft); }
@media(max-width:1050px) { .tc-object-list { grid-template-columns:1fr; } }
@media(max-width:760px) { .tc-object-toolbar { align-items:stretch; flex-direction:column; } .tc-object-add { width:100%; } }
@media(max-width:480px) { .tc-object-card { padding:15px; } .tc-object-card-heading { flex-direction:column; } .tc-object-badges { justify-content:flex-start; } .tc-object-assignments { grid-template-columns:1fr; } .tc-object-card-footer { align-items:stretch; flex-direction:column; } .tc-object-card-footer .tc-actions .tc-btn { flex:1; } }
.tc-pagination { margin:20px 0; padding:0; overflow:hidden; border:1px solid var(--tc-line); border-radius:12px; background:var(--tc-bg); }
.tc-pagination > div:first-child { padding:14px 18px; }
.tc-pagination label { white-space:nowrap; }
.tc-pagination nav { padding:0 16px 14px; }
.tc-pagination nav a,.tc-pagination nav span { min-width:40px; min-height:40px; justify-content:center; border-radius:8px; }
.tc-pagination nav a:focus-visible { z-index:1; outline:3px solid var(--tc-accent); outline-offset:2px; }
@media(max-width:640px) { .tc-pagination > div:first-child { align-items:flex-start; } .tc-pagination form { justify-content:space-between; } .tc-pagination nav { padding-right:10px; padding-left:10px; } .tc-pagination nav a,.tc-pagination nav span { min-width:36px; min-height:36px; padding:7px 9px; } }
.tc-object-toolbar-caption { margin:3px 0 0; color:var(--tc-muted); font-size:13px; }
.tc-object-toolbar { align-items:center; }
.tc-object-add { min-height:42px; }
.tc-object-summary { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:10px; }
.tc-object-summary > div,.tc-object-summary > a { min-height:70px; padding:11px 15px; border-radius:12px; transition:border-color .15s ease,background-color .15s ease,transform .15s ease; }
.tc-object-summary > a:hover { transform:translateY(-1px); }
.tc-object-summary > a.is-selected { border-color:var(--tc-accent); background:var(--tc-soft); box-shadow:inset 0 0 0 1px var(--tc-accent); }
.tc-object-summary-value { font-size:21px; line-height:1; }
.tc-object-summary-label { line-height:1.3; }
.tc-object-filter-panel { padding:18px 20px; }
.tc-object-filter-heading { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:16px; }
.tc-object-filter-heading h2 { margin:0 0 2px; font-size:17px; }
.tc-object-filter-heading p { margin:0; font-size:12px; }
.tc-btn-reset { min-height:38px; padding:7px 11px; font-size:12px; }
.tc-object-filter-form { display:grid; grid-template-columns:minmax(210px,1.5fr) repeat(3,minmax(150px,1fr)); gap:12px; align-items:end; }
.tc-object-filter-form .tc-field { margin:0; }
.tc-object-filter-form .tc-field label { font-size:12px; }
.tc-object-filter-form input:not([type=checkbox]),.tc-object-filter-form select { min-height:42px; padding:8px 10px; font-size:13px; }
.tc-object-filter-search { grid-column:span 2; }
.tc-object-filter-actions { display:flex; align-items:center; gap:10px; }
.tc-object-filter-actions .tc-btn { min-height:42px; white-space:nowrap; }
.tc-object-results-heading { display:flex; align-items:baseline; justify-content:space-between; gap:12px; margin:24px 2px 12px; }
.tc-object-results-heading h2 { margin:0; font-size:18px; }
.tc-object-results-heading span { color:var(--tc-muted); font-size:13px; }
.tc-object-card { border-color:var(--tc-line); box-shadow:0 2px 8px #18243a07; }
.tc-object-card:hover { border-color:var(--tc-accent); box-shadow:0 5px 16px #18243a0d; }
@media(max-width:1000px) { .tc-object-filter-form { grid-template-columns:repeat(2,minmax(0,1fr)); } .tc-object-filter-search { grid-column:span 2; } .tc-object-filter-actions { grid-column:span 2; } }
@media(max-width:640px) { .tc-object-toolbar { align-items:flex-start; } .tc-object-summary { grid-template-columns:1fr; } .tc-object-summary > div,.tc-object-summary > a { min-height:56px; } .tc-object-filter-panel { padding:16px 13px; } .tc-object-filter-heading { align-items:flex-start; flex-direction:column; } .tc-object-filter-form { grid-template-columns:1fr; } .tc-object-filter-search,.tc-object-filter-actions { grid-column:auto; } .tc-object-filter-actions .tc-btn { width:100%; } }

</style>
<script id="timcare-ti-forms-v2">

(function () {
    'use strict';
    const isManagedForm = form => form instanceof HTMLFormElement && (form.hasAttribute('data-tc-native') || form.closest('.tc'));
    // Preserve Laravel's native redirect, session flash, old input, and CSRF lifecycle.
    // Stop ONLY legacy bubbling submit interception; do not prevent the native submit.
    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (!isManagedForm(form)) return;
        event.stopPropagation();
        if (form.dataset.tcSubmitting === '1') { event.preventDefault(); return; }
        if (!form.checkValidity()) { event.preventDefault(); form.reportValidity(); return; }
        form.dataset.tcSubmitting = '1';
        form.setAttribute('aria-busy', 'true');
        const button = event.submitter;
        if (button && button.type === 'submit') {
            button.dataset.tcOriginalText = button.textContent;
            const isGetForm = form.method.toLowerCase() === 'get';
            button.textContent = isGetForm ? 'Memuat…' : 'Menyimpan…';
            // Submit buttons carry meaningful GET values (for example report type).
            // Disabled controls are omitted from the native query string.
            if (!isGetForm) button.disabled = true;
        }
    }, true);
    function initialize() {
        document.querySelectorAll('form').forEach(function (form) {
            if (!isManagedForm(form)) return;
            const feature = form.querySelector('[name="data[feature_update]"]');
            const notes = form.querySelector('[name="data[update_notes]"]');
            if (feature && notes) {
                const update = function () {
                    notes.required = feature.value === 'Ya';
                    notes.setAttribute('aria-required', notes.required ? 'true' : 'false');
                    const label = form.querySelector('label[for="' + notes.id + '"]');
                    if (label) {
                        let marker = label.querySelector('[data-tc-required-marker]');
                        if (notes.required && !marker) {
                            marker = document.createElement('span'); marker.dataset.tcRequiredMarker = '1';
                            marker.setAttribute('aria-hidden','true'); marker.textContent = ' *'; label.append(marker);
                        } else if (!notes.required && marker) marker.remove();
                    }
                };
                feature.addEventListener('change', update); update();
            }
            form.querySelectorAll('select[data-tc-long-option]').forEach(function (select) {
                const help = document.getElementById(select.id + '-selection');
                if (!help) return;
                const show = function () { help.textContent = select.value || ''; };
                select.addEventListener('change', show); show();
            });
        });
        const filterType = document.getElementById('filter-type');
        const filterPeriod = document.getElementById('filter-period');
        if (filterType && filterPeriod) {
            const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            const updatePeriod = function () {
                const selected = filterPeriod.value;
                const semester = filterType.value === 'followup';
                const label = document.querySelector('label[for="filter-period"]');
                if (label) label.textContent = semester ? 'Semester' : 'Bulan';
                filterPeriod.replaceChildren(new Option('Semua periode',''));
                (semester ? ['Semester I','Semester II'] : months).forEach(function (name,index) {
                    filterPeriod.add(new Option(name,String(index+1)));
                });
                filterPeriod.value = [...filterPeriod.options].some(o => o.value === selected) ? selected : '';
            };
            filterType.addEventListener('change', updatePeriod); updatePeriod();
        }
        const summary = document.querySelector('[data-tc-errors]');
        if (summary) summary.focus({preventScroll:false});
        document.querySelectorAll('[data-tc-member-search]').forEach(function (search) {
            const list = search.closest('.tc-members-panel')?.querySelector('.tc-member-list');
            if (!list) return;
            const rows = [...list.querySelectorAll('[data-member-search]')];
            const empty = list.querySelector('[data-tc-member-empty]');
            const count = search.closest('.tc-members-panel').querySelector('[data-tc-member-count]');
            const update = function () {
                const query = search.value.trim().toLocaleLowerCase();
                let visible = 0;
                rows.forEach(function (row) {
                    const match = row.dataset.memberSearch.includes(query);
                    row.hidden = !match;
                    if (match) visible++;
                });
                if (empty) empty.hidden = visible !== 0;
                if (count) count.textContent = list.querySelectorAll('input[type="checkbox"]:checked').length;
            };
            search.addEventListener('input', update);
            list.addEventListener('change', update);
            update();
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initialize, {once:true});
    else initialize();
    window.addEventListener('pageshow', function () {
        document.querySelectorAll('form[data-tc-submitting="1"]').forEach(function (form) {
            delete form.dataset.tcSubmitting; form.removeAttribute('aria-busy');
            form.querySelectorAll('button[data-tc-original-text]').forEach(function (button) {
                button.textContent = button.dataset.tcOriginalText; button.disabled = false;
                delete button.dataset.tcOriginalText;
            });
        });
    });
})();

</script>
@endonce
