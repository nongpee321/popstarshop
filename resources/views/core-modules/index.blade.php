@extends('layout')

@section('title', 'คู่มือ PopStar 4M')
@section('page-title', 'คู่มือ PopStar 4M')
@section('page-subtitle', 'ระบบงาน คน เงิน สินค้า และการบริหารของ PopStar Shop')

@push('head')
<style>
    [x-cloak] { display: none !important; }

    .manual-shell { display: grid; gap: 14px; min-width: 0; color: var(--erp-text); }
    .manual-panel { min-width: 0; overflow: hidden; background: #fff; border: 1px solid var(--erp-border); border-radius: 8px; }
    .manual-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 18px; padding: 18px 20px; border-top: 4px solid var(--erp-primary); }
    .manual-eyebrow { color: var(--erp-info); font-size: 11px; font-weight: 900; text-transform: uppercase; }
    .manual-title { margin: 3px 0 5px; color: var(--erp-text); font-size: 24px; font-weight: 900; line-height: 1.2; }
    .manual-lead { margin: 0; max-width: 860px; color: var(--erp-muted); font-size: 13px; line-height: 1.55; }
    .manual-actions { display: flex; gap: 8px; flex: 0 0 auto; }
    .manual-icon-button { width: 38px; height: 38px; display: grid; place-items: center; border: 1px solid var(--erp-border); border-radius: 7px; background: #fff; color: var(--erp-primary-dark); }
    .manual-icon-button:hover { color: var(--erp-primary); border-color: var(--erp-primary); }

    .manual-toolbar { display: grid; grid-template-columns: minmax(240px, 1fr) auto; gap: 12px; padding: 12px; }
    .manual-search { position: relative; }
    .manual-search i { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: var(--erp-muted); }
    .manual-search input { width: 100%; height: 40px; padding: 0 12px 0 38px; border: 1px solid var(--erp-border); border-radius: 7px; color: var(--erp-text); background: #fff; }
    .manual-search input:focus { outline: 2px solid rgba(21, 153, 211, .16); border-color: var(--erp-primary); }
    .manual-segments { display: flex; gap: 4px; padding: 3px; border: 1px solid var(--erp-border); border-radius: 7px; background: var(--erp-surface-2); }
    .manual-segment { min-width: 100px; height: 32px; padding: 0 12px; border: 0; border-radius: 5px; background: transparent; color: var(--erp-muted); font-size: 12px; font-weight: 900; }
    .manual-segment.active { color: #fff; background: var(--erp-primary-dark); }

    .pillar-summary { display: grid; grid-template-columns: 46px minmax(0, 1fr); gap: 12px; align-items: start; padding: 16px 18px 12px; border-bottom: 1px solid var(--erp-surface-2); }
    .pillar-icon { width: 42px; height: 42px; display: grid; place-items: center; border-radius: 7px; font-size: 19px; }
    .tone-teal { color: var(--erp-info); background: var(--erp-border); }
    .tone-red { color: var(--erp-danger); background: var(--erp-danger-soft); }
    .tone-blue { color: var(--erp-primary-ink); background: var(--erp-primary-soft); }
    .tone-amber { color: var(--erp-warning-ink); background: var(--erp-warning-soft); }
    .pillar-name { margin: 0 0 3px; color: var(--erp-text); font-size: 17px; font-weight: 900; }
    .pillar-copy { margin: 0; color: var(--erp-muted); font-size: 13px; line-height: 1.5; }

    .manual-table-wrap { overflow-x: auto; }
    .manual-table { width: 100%; min-width: 760px; border-collapse: collapse; }
    .manual-table th { padding: 9px 12px; color: var(--erp-muted); background: var(--erp-surface-2); border-bottom: 1px solid var(--erp-border); font-size: 11px; font-weight: 900; text-transform: uppercase; }
    .manual-table td { padding: 10px 12px; color: var(--erp-muted); border-bottom: 1px solid var(--erp-surface-2); font-size: 12px; vertical-align: middle; }
    .manual-table tbody tr:last-child td { border-bottom: 0; }
    .manual-table strong { color: var(--erp-text); font-size: 13px; }
    .program-link, .program-locked { width: 32px; height: 32px; display: inline-grid; place-items: center; border-radius: 6px; }
    .program-link { color: var(--erp-primary); background: var(--erp-primary-soft); }
    .program-link:hover { color: #fff; background: var(--erp-primary); }
    .program-locked { color: #91a3af; background: var(--erp-primary-soft); }

    .manual-section-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 18px; border-bottom: 1px solid var(--erp-border); }
    .manual-section-head h2 { margin: 0; color: var(--erp-text); font-size: 16px; font-weight: 900; }
    .manual-section-head span { color: var(--erp-muted); font-size: 12px; }
    .flow-tabs { display: flex; gap: 6px; padding: 10px 12px; overflow-x: auto; border-bottom: 1px solid var(--erp-surface-2); }
    .flow-tab { flex: 0 0 auto; height: 34px; padding: 0 12px; border: 1px solid var(--erp-border); border-radius: 6px; color: var(--erp-muted); background: #fff; font-size: 12px; font-weight: 800; }
    .flow-tab.active { color: #fff; border-color: var(--erp-primary); background: var(--erp-primary); }
    .flow-content { padding: 16px 18px 18px; }
    .flow-meta { display: grid; grid-template-columns: minmax(180px, .45fr) 1fr; gap: 12px; margin-bottom: 14px; }
    .flow-meta div { padding: 10px 12px; border-left: 3px solid var(--erp-primary); background: var(--erp-surface-2); }
    .flow-meta small { display: block; color: var(--erp-muted); font-size: 10px; font-weight: 900; text-transform: uppercase; }
    .flow-meta strong { color: var(--erp-primary-dark); font-size: 12px; }
    .flow-steps { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 8px; }
    .flow-step { position: relative; min-height: 112px; padding: 11px; border: 1px solid var(--erp-border); border-radius: 7px; background: #fff; }
    .flow-step:not(:last-child)::after { content: '\F285'; position: absolute; right: -12px; top: 42px; z-index: 2; color: var(--erp-primary); font-family: bootstrap-icons; font-size: 14px; }
    .flow-number { display: block; color: var(--erp-primary); font-size: 10px; font-weight: 900; }
    .flow-step strong { display: block; margin: 4px 0; color: var(--erp-text); font-size: 12px; }
    .flow-step p { margin: 0; color: var(--erp-muted); font-size: 11px; line-height: 1.4; }
    .flow-step a { color: inherit; text-decoration: none; }
    .formula-policy { display: grid; grid-template-columns: repeat(4,minmax(0,1fr)); border-bottom: 1px solid var(--erp-border); background: var(--erp-surface-2); }
    .formula-policy div { padding: 11px 13px; border-right: 1px solid var(--erp-border); }
    .formula-policy div:last-child { border-right: 0; }
    .formula-policy span,.formula-policy strong { display: block; }
    .formula-policy span { color: var(--erp-muted); font-size: 10px; }
    .formula-policy strong { margin-top: 2px; color: var(--erp-text); font-size: 12px; }
    .formula-table { min-width: 1180px; }
    .formula-table code { color: var(--erp-info); background: var(--erp-success-soft); font-size: 11px; white-space: normal; }
    .formula-table small { display: block; margin-top: 4px; color: var(--erp-muted); line-height: 1.45; }

    .manual-two-col { display: grid; grid-template-columns: 1.05fr .95fr; gap: 14px; }
    .gap-list { display: grid; }
    .gap-row { display: grid; grid-template-columns: 86px minmax(0, 1fr); gap: 12px; padding: 12px 16px; border-bottom: 1px solid var(--erp-surface-2); }
    .gap-row:last-child { border-bottom: 0; }
    .gap-status { align-self: start; padding: 4px 7px; border-radius: 5px; text-align: center; font-size: 10px; font-weight: 900; }
    .gap-critical { color: var(--erp-danger); background: var(--erp-danger-soft); }
    .gap-control { color: var(--erp-warning-ink); background: var(--erp-warning-soft); }
    .gap-growth { color: var(--erp-success-ink); background: var(--erp-success-soft); }
    .gap-row h3 { margin: 0 0 3px; color: var(--erp-primary-dark); font-size: 13px; font-weight: 900; }
    .gap-row p { margin: 0; color: var(--erp-muted); font-size: 11px; line-height: 1.45; }
    .routine-row { padding: 12px 16px; border-bottom: 1px solid var(--erp-surface-2); }
    .routine-row:last-child { border-bottom: 0; }
    .routine-meta { display: flex; justify-content: space-between; gap: 8px; margin-bottom: 6px; }
    .routine-meta strong { color: var(--erp-text); font-size: 12px; }
    .routine-meta span { color: var(--erp-muted); font-size: 11px; }
    .routine-items { margin: 0; padding-left: 18px; color: var(--erp-muted); font-size: 11px; line-height: 1.65; }
    .control-manuals { display: grid; gap: 10px; padding: 12px; }
    .control-manual { border: 1px solid var(--erp-border); border-radius: 7px; overflow: hidden; }
    .control-title { width: 100%; display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 11px 13px; border: 0; background: var(--erp-surface-2); color: var(--erp-text); text-align: left; font-size: 13px; font-weight: 900; }
    .control-body { padding: 14px; }
    .control-owner { padding: 9px 11px; border-left: 3px solid var(--erp-primary); background: var(--erp-surface-2); color: var(--erp-muted); font-size: 11px; }
    .control-grid { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 12px; margin-top: 12px; }
    .control-block h3 { margin: 0 0 6px; color: var(--erp-primary-dark); font-size: 12px; font-weight: 900; }
    .control-block ol,.control-block ul { margin: 0; padding-left: 18px; color: var(--erp-muted); font-size: 11px; line-height: 1.65; }
    .control-purpose { margin: 9px 0 0; color: var(--erp-muted); font-size: 12px; line-height: 1.5; }
    .benchmark-summary { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 8px; padding: 12px; border-bottom: 1px solid var(--erp-surface-2); }
    .benchmark-summary div { padding: 10px 12px; border: 1px solid var(--erp-border); border-radius: 7px; background: var(--erp-surface-2); }
    .benchmark-summary span { display: block; color: var(--erp-muted); font-size: 10px; font-weight: 800; }
    .benchmark-summary strong { display: block; margin-top: 2px; color: var(--erp-text); font-size: 18px; }
    .benchmark-status { display: inline-block; min-width: 64px; padding: 4px 6px; border-radius: 5px; text-align: center; font-size: 10px; font-weight: 900; }
    .benchmark-ready { color: var(--erp-success-ink); background: var(--erp-success-soft); }
    .benchmark-partial { color: var(--erp-warning-ink); background: var(--erp-warning-soft); }
    .benchmark-planned { color: var(--erp-danger); background: var(--erp-danger-soft); }
    .source-list { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 8px; padding: 12px; }
    .source-item { display: grid; grid-template-columns: minmax(0,1fr) 32px; gap: 8px; align-items: center; padding: 10px 11px; border: 1px solid var(--erp-border); border-radius: 7px; }
    .source-item strong { display: block; color: var(--erp-primary-dark); font-size: 12px; }
    .source-item span { display: block; margin-top: 2px; color: var(--erp-muted); font-size: 10px; line-height: 1.4; }
    .coverage-note { margin: 12px; padding: 11px 13px; border-left: 3px solid var(--erp-danger); background: var(--erp-danger-soft); color: #6b4b50; font-size: 11px; line-height: 1.55; }
    .uat-summary { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); border-bottom: 1px solid var(--erp-surface-2); }
    .uat-summary div { padding: 13px 16px; border-right: 1px solid var(--erp-surface-2); }
    .uat-summary div:last-child { border-right: 0; }
    .uat-summary span { display: block; color: var(--erp-muted); font-size: 10px; font-weight: 800; }
    .uat-summary strong { display: block; margin-top: 2px; color: var(--erp-text); font-size: 20px; }
    .uat-summary .pass strong { color: var(--erp-success-ink); }
    .uat-summary .fail strong { color: var(--erp-danger); }
    .uat-policy { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px; padding: 12px; background: var(--erp-surface-2); border-bottom: 1px solid var(--erp-surface-2); }
    .uat-policy div { padding: 9px 10px; border-left: 3px solid var(--erp-primary); background: #fff; color: var(--erp-muted); font-size: 11px; line-height: 1.45; }
    .uat-policy strong { display: block; margin-bottom: 2px; color: var(--erp-primary-dark); }
    .uat-toolbar { display: grid; grid-template-columns: minmax(230px, 1fr) auto; gap: 10px; align-items: center; padding: 12px; border-bottom: 1px solid var(--erp-surface-2); }
    .uat-tabs { display: flex; gap: 6px; padding: 10px 12px; overflow-x: auto; border-bottom: 1px solid var(--erp-surface-2); }
    .uat-tab { border: 1px solid var(--erp-border); border-radius: 6px; background: #fff; color: var(--erp-muted); padding: 7px 10px; font-size: 11px; font-weight: 900; white-space: nowrap; }
    .uat-tab.active { border-color: var(--erp-primary); background: var(--erp-primary-soft); color: var(--erp-primary-ink); }
    .uat-suite-head { display: flex; justify-content: space-between; gap: 14px; padding: 12px 16px; background: var(--erp-surface-2); border-bottom: 1px solid var(--erp-surface-2); }
    .uat-suite-head strong { display: block; color: var(--erp-text); font-size: 13px; }
    .uat-suite-head span { display: block; margin-top: 2px; color: var(--erp-muted); font-size: 11px; }
    .uat-case { display: grid; grid-template-columns: 92px minmax(210px, .7fr) minmax(420px, 1.3fr) 154px; gap: 12px; padding: 13px 16px; border-bottom: 1px solid var(--erp-surface-2); }
    .uat-case:last-child { border-bottom: 0; }
    .uat-case-id { color: var(--erp-text); font-family: Consolas, monospace; font-size: 12px; font-weight: 900; }
    .uat-risk { display: block; width: fit-content; margin-top: 5px; padding: 3px 6px; border-radius: 4px; font-size: 9px; font-weight: 900; }
    .uat-risk-critical { color: var(--erp-danger); background: var(--erp-danger-soft); }
    .uat-risk-control { color: var(--erp-warning-ink); background: var(--erp-warning-soft); }
    .uat-case-title { color: var(--erp-primary-dark); font-size: 12px; font-weight: 900; line-height: 1.35; }
    .uat-prepare { margin-top: 5px; color: var(--erp-muted); font-size: 10.5px; line-height: 1.45; }
    .uat-steps { margin: 0; padding-left: 18px; color: var(--erp-muted); font-size: 11px; line-height: 1.5; }
    .uat-expected { margin-top: 7px; padding: 7px 9px; border-left: 3px solid #16a085; background: var(--erp-primary-soft); color: var(--erp-text); font-size: 10.5px; line-height: 1.45; }
    .uat-evidence { margin-top: 5px; color: var(--erp-muted); font-size: 10px; }
    .uat-actions { display: grid; grid-template-columns: repeat(3, 1fr); gap: 5px; align-content: start; }
    .uat-status { height: 30px; border: 1px solid var(--erp-border); border-radius: 6px; background: #fff; color: var(--erp-muted); font-size: 10px; font-weight: 900; }
    .uat-status.pass.active { border-color: var(--erp-success-ink); background: var(--erp-success-soft); color: var(--erp-success-ink); }
    .uat-status.fail.active { border-color: var(--erp-danger); background: var(--erp-danger-soft); color: var(--erp-danger); }
    .uat-status.pending.active { border-color: var(--erp-muted); background: var(--erp-border); color: var(--erp-text); }
    .uat-open { grid-column: 1 / -1; display: flex; justify-content: center; gap: 6px; align-items: center; height: 30px; border: 1px solid var(--erp-primary); border-radius: 6px; color: var(--erp-primary-ink); text-decoration: none; font-size: 10px; font-weight: 900; }
    .uat-open:hover { background: var(--erp-primary-soft); color: var(--erp-primary-ink); }

    /* ── Visual Step-by-Step Guide ────────────────────────── */
    .vg-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 18px 22px; border-bottom: 1px solid var(--erp-border); background: linear-gradient(135deg, rgba(21,153,211,.08), rgba(189,40,54,.05)); }
    .vg-header h2 { margin: 0; font-size: 19px; font-weight: 900; color: var(--erp-text); }
    .vg-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px; padding: 22px; }
    .vg-card { border: 1px solid var(--erp-border); border-radius: 12px; overflow: hidden; background: #fff; box-shadow: 0 4px 18px rgba(0,0,0,.04); transition: transform .2s, box-shadow .2s; display: flex; flex-direction: column; cursor: pointer; text-decoration: none; color: inherit; }
    .vg-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.08); border-color: var(--erp-primary); }
    .vg-card-thumb { height: 165px; position: relative; overflow: hidden; background: #f1f5f9; border-bottom: 1px solid var(--erp-border); }
    .vg-card-thumb img { width: 100%; height: 100%; object-fit: cover; object-position: top center; transition: transform .3s; }
    .vg-card:hover .vg-card-thumb img { transform: scale(1.04); }
    .vg-card-badge { position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,.75); color: #fff; font-size: 11px; font-weight: 800; padding: 3px 9px; border-radius: 6px; backdrop-filter: blur(4px); }
    .vg-card-body { padding: 16px; flex: 1 1 auto; display: flex; flex-direction: column; }
    .vg-card-title { font-size: 16px; font-weight: 900; color: var(--erp-text); margin: 0 0 6px; display: flex; align-items: center; gap: 8px; }
    .vg-card-desc { font-size: 12.5px; color: var(--erp-muted); line-height: 1.5; margin: 0 0 14px; flex: 1 1 auto; }
    .vg-card-steps-count { display: flex; align-items: center; gap: 6px; font-size: 11.5px; font-weight: 800; color: var(--erp-primary-ink); background: var(--erp-primary-soft); padding: 6px 10px; border-radius: 6px; margin-bottom: 12px; }
    .vg-card-btn { width: 100%; padding: 10px; border-radius: 8px; font-size: 13px; font-weight: 900; text-align: center; border: 0; background: #b91c1c; color: #fff; transition: background .15s; display: flex; align-items: center; justify-content: center; gap: 6px; }
    .vg-card:hover .vg-card-btn { background: #991b1b; }

    /* Reader Modal */
    .vg-modal-backdrop { position: fixed; inset: 0; z-index: 10000; background: rgba(15,23,42,.75); backdrop-filter: blur(5px); display: flex; align-items: center; justify-content: center; padding: 16px; }
    .vg-modal-card { width: min(1000px, 98vw); max-height: 94vh; background: #fff; border-radius: 14px; box-shadow: 0 25px 70px rgba(0,0,0,.35); display: flex; flex-direction: column; overflow: hidden; }
    .vg-modal-head { display: flex; justify-content: space-between; align-items: center; padding: 14px 22px; background: linear-gradient(135deg, #b91c1c, #991b1b); color: #fff; border-bottom: 1px solid rgba(0,0,0,.1); }
    .vg-modal-title { font-size: 17px; font-weight: 900; color: #fff; margin: 0; display: flex; align-items: center; gap: 8px; }
    .vg-modal-close { width: 34px; height: 34px; border-radius: 50%; border: 0; background: rgba(255,255,255,.2); color: #fff; font-size: 18px; cursor: pointer; display: grid; place-items: center; }
    .vg-modal-close:hover { background: rgba(255,255,255,.35); }
    .vg-step-nav { display: flex; gap: 6px; padding: 10px 18px; background: var(--erp-surface-2); border-bottom: 1px solid var(--erp-border); overflow-x: auto; }
    .vg-step-tab { padding: 8px 14px; border-radius: 8px; border: 1px solid var(--erp-border); background: #fff; color: var(--erp-text); font-size: 12px; font-weight: 800; cursor: pointer; white-space: nowrap; transition: all .15s; display: flex; align-items: center; gap: 6px; }
    .vg-step-tab:hover { border-color: var(--erp-primary); color: var(--erp-primary); }
    .vg-step-tab.active { background: var(--erp-primary); color: #fff; border-color: var(--erp-primary); box-shadow: 0 3px 8px rgba(21,153,211,.25); }
    .vg-modal-body { padding: 20px 22px; overflow-y: auto; flex: 1 1 auto; display: grid; grid-template-columns: minmax(340px, 1.2fr) 1fr; gap: 20px; align-items: start; }
    .vg-modal-img-wrap { border: 1px solid var(--erp-border); border-radius: 10px; overflow: hidden; background: #0f172a; position: relative; cursor: zoom-in; }
    .vg-modal-img-wrap img { width: 100%; height: auto; max-height: 480px; object-fit: contain; display: block; }
    .vg-modal-img-tip { position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,.7); color: #fff; font-size: 10.5px; font-weight: 700; padding: 3px 8px; border-radius: 5px; pointer-events: none; }
    .vg-modal-detail { display: flex; flex-direction: column; gap: 12px; }
    .vg-step-badge { width: fit-content; padding: 4px 10px; border-radius: 6px; background: var(--erp-primary-soft); color: var(--erp-primary-ink); font-size: 12px; font-weight: 900; }
    .vg-step-title { font-size: 17px; font-weight: 900; color: var(--erp-text); margin: 0; line-height: 1.35; }
    .vg-step-desc { font-size: 13.5px; color: var(--erp-muted); line-height: 1.6; margin: 0; }
    .vg-step-bullets { margin: 0; padding-left: 18px; color: var(--erp-text); font-size: 12.5px; line-height: 1.65; }
    .vg-step-tip { padding: 12px 14px; border-radius: 8px; background: #fffbeb; border: 1px solid #fde68a; color: #92400e; font-size: 12px; line-height: 1.5; display: flex; gap: 8px; align-items: flex-start; }
    .vg-modal-foot { padding: 12px 22px; background: var(--erp-surface-2); border-top: 1px solid var(--erp-border); display: flex; justify-content: space-between; align-items: center; }

    /* Lightbox Zoom */
    .vg-lightbox { position: fixed; inset: 0; z-index: 10010; background: rgba(15,23,42,.88); backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; padding: 20px; }
    .vg-lightbox img { max-width: 95vw; max-height: 90vh; border-radius: 10px; box-shadow: 0 20px 60px rgba(0,0,0,.5); }
    .vg-lightbox-close { position: absolute; top: 20px; right: 20px; width: 44px; height: 44px; border-radius: 50%; border: 0; background: rgba(255,255,255,.2); color: #fff; font-size: 24px; cursor: pointer; display: grid; place-items: center; }
    .vg-lightbox-close:hover { background: rgba(255,255,255,.4); }

    html[data-color-mode="night"] .vg-card,
    html[data-color-mode="night"] .vg-modal-card,
    html[data-color-mode="night"] .vg-step-tab { background: #2a3441 !important; border-color: #3b4758 !important; }
    html[data-color-mode="night"] .vg-card-title,
    html[data-color-mode="night"] .vg-step-title,
    html[data-color-mode="night"] .vg-step-bullets { color: #f8fafc !important; }
    html[data-color-mode="night"] .vg-card-desc,
    html[data-color-mode="night"] .vg-step-desc { color: #cbd5e1 !important; }
    html[data-color-mode="night"] .vg-step-tab.active { background: var(--erp-primary) !important; color: #fff !important; }
    html[data-color-mode="night"] .vg-step-tip { background: rgba(245,158,11,.15) !important; border-color: rgba(245,158,11,.3) !important; color: #fde68a !important; }

    @media (max-width: 820px) {
        .vg-modal-body { grid-template-columns: 1fr; }
    }

    @media (max-width: 1100px) {
        .flow-steps { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .flow-step:nth-child(3)::after { display: none; }
        .manual-two-col { grid-template-columns: 1fr; }
        .uat-case { grid-template-columns: 82px minmax(180px, .7fr) minmax(300px, 1.3fr); }
        .uat-actions { grid-column: 2 / -1; grid-template-columns: repeat(4, minmax(90px, 1fr)); }
        .uat-open { grid-column: auto; }
    }
    @media (max-width: 720px) {
        .manual-header { flex-direction: column; padding: 15px; }
        .manual-title { font-size: 20px; }
        .manual-toolbar { grid-template-columns: 1fr; }
        .manual-segments { overflow-x: auto; }
        .manual-segment { min-width: 84px; }
        .formula-policy { grid-template-columns: 1fr 1fr; }
        .flow-meta { grid-template-columns: 1fr; }
        .flow-steps { grid-template-columns: 1fr; }
        .flow-step { min-height: 0; }
        .flow-step::after { display: none !important; }
        .gap-row { grid-template-columns: 74px minmax(0, 1fr); padding: 11px 12px; }
        .control-grid { grid-template-columns: 1fr; }
        .benchmark-summary, .source-list { grid-template-columns: 1fr; }
        .uat-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .uat-summary div:nth-child(2) { border-right: 0; }
        .uat-summary div:nth-child(-n+2) { border-bottom: 1px solid var(--erp-surface-2); }
        .uat-policy { grid-template-columns: 1fr; }
        .uat-toolbar { grid-template-columns: 1fr; }
        .uat-case { grid-template-columns: 1fr; }
        .uat-actions { grid-column: auto; grid-template-columns: repeat(3, 1fr); }
        .uat-open { grid-column: 1 / -1; }
    }
    @media print {
        .app-header, .app-sidebar, .manual-toolbar, .manual-actions, .flow-tabs, .uat-toolbar, .uat-actions { display: none !important; }
        .app-main { margin: 0 !important; }
        .app-content { padding: 0 !important; }
        .manual-shell { gap: 8px; }
        .manual-panel { break-inside: avoid; }
        [x-cloak] { display: block !important; }
        .uat-case { grid-template-columns: 70px 200px 1fr; break-inside: avoid; }
    }
</style>
@endpush

@section('content')
@php
    $routeAccess = function (string $routeName): bool {
        if (! \Illuminate\Support\Facades\Route::has($routeName)) {
            return false;
        }

        $permission = \App\Support\RoutePermissions::resolve($routeName);

        return $permission === null || (auth()->user() && auth()->user()->hasPermission($permission));
    };
@endphp

<div class="manual-shell" x-data="{
    pillar: 'man',
    flow: 'pos',
    query: '',
    testSuite: 'master',
    testQuery: '',
    testResults: {},
    testTotal: {{ collect($testSuites)->sum(fn ($suite) => count($suite['cases'])) }},
    init() {
        try {
            this.testResults = JSON.parse(localStorage.getItem('popstar-erp-uat') || '{}') || {};
        } catch (error) {
            this.testResults = {};
        }
    },
    setTest(id, status) {
        this.testResults = { ...this.testResults, [id]: status };
        localStorage.setItem('popstar-erp-uat', JSON.stringify(this.testResults));
    },
    testCount(status) {
        return Object.values(this.testResults).filter(value => value === status).length;
    },
    resetTests() {
        if (!confirm('ล้างผลการทดสอบทั้งหมดในเครื่องนี้หรือไม่')) return;
        this.testResults = {};
        localStorage.removeItem('popstar-erp-uat');
    }
}">
    <section class="manual-panel manual-header">
        <div>
            <div class="manual-eyebrow">PopStar 4M ERP Handbook</div>
            <h1 class="manual-title">คู่มือการทำงาน ERP แบบครบวงจร</h1>
            <p class="manual-lead">จุดอ้างอิงกลางสำหรับงานขาย ซื้อ คลัง ผลิต การเงิน บัญชี และการบริหาร แบ่งระบบตาม MAN, MONEY, MATERIAL และ MANAGEMENT พร้อมเส้นทางข้อมูลและรายการที่ยังต้องพัฒนา</p>
        </div>
        <div class="manual-actions" style="display:flex; align-items:center; gap:8px;">
            <a href="{{ route('manual') }}" target="_blank" class="btn btn-sm btn-outline-danger fw-bold d-inline-flex align-items-center gap-1" title="เปิดคู่มือฉบับเต็มภายนอก (Public Visual Guide) หรือพิมพ์ PDF">
                <i class="bi bi-box-arrow-up-right"></i> เปิดคู่มือฉบับเต็ม (Public Guide)
            </a>
            <button type="button" class="manual-icon-button" title="พิมพ์คู่มือ" aria-label="พิมพ์คู่มือ" onclick="window.print()"><i class="bi bi-printer"></i></button>
        </div>
    </section>

    {{-- ── 📸 คู่มือการใช้งานพร้อมภาพประกอบหน้าจอจริง (Visual Step-by-Step Guide) ── --}}
    <section class="manual-panel" id="visual-guide">
        <div class="vg-header">
            <div>
                <span class="manual-eyebrow" style="color:var(--erp-danger)"><i class="bi bi-camera-fill me-1"></i> Visual Guide & SOP 8 Chapters</span>
                <h2>📸 คู่มือมาตรฐาน 8 บทหลักพร้อมภาพประกอบหน้าจอจริง (SOP Manual)</h2>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('manual') }}" target="_blank" class="badge text-bg-primary rounded-pill px-3 py-2 text-decoration-none fw-bold" style="font-size:12px">
                    <i class="bi bi-book me-1"></i> เปิดดูคู่มือฉบับเต็มภายนอก
                </a>
                <span class="badge text-bg-danger rounded-pill px-3 py-2 fw-bold" style="font-size:12px">ครบ 8 บท 14 โมดูล</span>
            </div>
        </div>

        {{-- Grid of 14 Modules Organized by 8 Chapters --}}
        <div class="vg-grid">
            {{-- ==================== บทที่ 1: ภาพรวม & การเริ่มต้นใช้งาน ==================== --}}
            {{-- Card 1.1: Overview --}}
            <div class="vg-card" onclick="openManualReader('overview')">
                <div class="vg-card-thumb">
                    <img src="{{ asset('images/manual/overview_main.png') }}" alt="ภาพรวมและศูนย์ควบคุม">
                    <span class="vg-card-badge" style="background:#2563eb;"><i class="bi bi-speedometer2 me-1"></i> บทที่ 1.1: 9 ฟังก์ชันพร้อมรูปจริง</span>
                </div>
                <div class="vg-card-body">
                    <h3 class="vg-card-title"><i class="bi bi-speedometer2 text-primary"></i> 1.1 ภาพรวม & ศูนย์ควบคุม (Overview)</h3>
                    <p class="vg-card-desc">คู่มือแดชบอร์ดผู้บริหาร POPSTAR EXECUTIVE BOARD วิเคราะห์ยอดขายด่วน กำไรขั้นต้น อัตรากำไร และศูนย์ควบคุมต้นทุน งบประมาณ (Budget) เวลา/Payroll และมาตรการบริหาร</p>
                    <div class="vg-card-steps-count">
                        <i class="bi bi-layers-fill"></i> มีภาพประกอบ 9 ฟังก์ชันอย่างละเอียด
                    </div>
                    <button type="button" class="vg-card-btn" style="background:#2563eb;">
                        <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 1.1 (9 ฟังก์ชัน)
                    </button>
                </div>
            </div>

            {{-- Card 1.2: Users --}}
            <div class="vg-card" onclick="openManualReader('users')">
                <div class="vg-card-thumb">
                    <img src="{{ asset('images/manual/users_main.png') }}" alt="ผู้ใช้และสิทธิ์การใช้งาน">
                    <span class="vg-card-badge" style="background:#059669;"><i class="bi bi-shield-lock-fill me-1"></i> บทที่ 1.2: 8 ฟังก์ชันพร้อมรูปจริง</span>
                </div>
                <div class="vg-card-body">
                    <h3 class="vg-card-title"><i class="bi bi-people-fill text-success"></i> 1.2 ผู้ใช้ & สิทธิ์ (Users & Security)</h3>
                    <p class="vg-card-desc">คู่มือทะเบียนผู้ใช้งาน, กำหนดบทบาทสิทธิ์ (Roles), สิทธิ์การเข้าถึงเมนู, ผูกสาขาประจำตัวพนักงาน, รีเซ็ตรหัสผ่าน, รีเซ็ต PIN แคชเชียร์ POS, และบันทึกประวัติการตรวจสอบ (Audit Trail)</p>
                    <div class="vg-card-steps-count">
                        <i class="bi bi-layers-fill"></i> มีภาพประกอบ 8 ฟังก์ชันอย่างละเอียด
                    </div>
                    <button type="button" class="vg-card-btn" style="background:#059669;">
                        <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 1.2 (8 ฟังก์ชัน)
                    </button>
                </div>
            </div>

            {{-- Card 1.3: Settings --}}
            <div class="vg-card" onclick="openManualReader('settings')">
                <div class="vg-card-thumb">
                    <img src="{{ asset('images/manual/settings_main.png') }}" alt="ตั้งค่าระบบและเอกสาร">
                    <span class="vg-card-badge" style="background:#2563eb;"><i class="bi bi-gear-fill me-1"></i> บทที่ 1.3: 14 ฟังก์ชันพร้อมรูปจริง</span>
                </div>
                <div class="vg-card-body">
                    <h3 class="vg-card-title"><i class="bi bi-gear-wide-connected text-primary"></i> 1.3 ตั้งค่าระบบ & เอกสาร (Settings)</h3>
                    <p class="vg-card-desc">คู่มือตั้งค่าฟังก์ชันเอกสาร, กฎภาษี VAT 7%, เลขรันอัตโนมัติ, โลโก้/ตราประทับ, หมายเหตุเอกสาร, ข้อมูลกิจการ, ศูนย์ควบคุมโมดูล, POS Designer, จัดลำดับเมนู, ปรับธีม, ผูกบัญชี GL, ออกแบบใบเสร็จ, สายการอนุมัติ และความปลอดภัย</p>
                    <div class="vg-card-steps-count">
                        <i class="bi bi-sliders"></i> มีภาพประกอบ 14 ฟังก์ชันอย่างละเอียด
                    </div>
                    <button type="button" class="vg-card-btn" style="background:#2563eb;">
                        <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 1.3 (14 ฟังก์ชัน)
                    </button>
                </div>
            </div>

            {{-- ==================== บทที่ 2: ระบบขายหน้าร้าน (Point of Sale) ==================== --}}
            {{-- Card 2.1: Python POS --}}
            <div class="vg-card" onclick="openManualReader('python_pos')">
                <div class="vg-card-thumb">
                    <img src="{{ asset('images/manual/python_pos_main.png') }}" alt="แอปพลิเคชัน Python POS">
                    <span class="vg-card-badge"><i class="bi bi-terminal-fill me-1"></i> บทที่ 2.1: 6 ขั้นตอนพร้อมรูปจริง</span>
                </div>
                <div class="vg-card-body">
                    <h3 class="vg-card-title"><i class="bi bi-terminal-fill text-danger"></i> 2.1 แอปแคชเชียร์ (Python POS)</h3>
                    <p class="vg-card-desc">คู่มือใช้งานโปรแกรมแคชเชียร์หน้าร้านจริง เข้าสู่ระบบ เปิดกะ ยิงบาร์โค้ด ค้นหาสินค้า ชำระเงินสด/QR Code และปิดกะทำงาน</p>
                    <div class="vg-card-steps-count">
                        <i class="bi bi-layers-fill"></i> มีภาพประกอบ 6 ขั้นตอนอย่างละเอียด
                    </div>
                    <button type="button" class="vg-card-btn">
                        <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 2.1 (6 ขั้นตอน)
                    </button>
                </div>
            </div>

            {{-- Card 2.2: Web POS --}}
            <div class="vg-card" onclick="openManualReader('pos')">
                <div class="vg-card-thumb">
                    <img src="{{ asset('images/manual/pos_main.png') }}" alt="หน้าจอ POS">
                    <span class="vg-card-badge"><i class="bi bi-camera me-1"></i> บทที่ 2.2: 5 ขั้นตอนพร้อมรูป</span>
                </div>
                <div class="vg-card-body">
                    <h3 class="vg-card-title"><i class="bi bi-cart4 text-danger"></i> 2.2 วิธีขายหน้าร้าน (JET POS)</h3>
                    <p class="vg-card-desc">คู่มือการเปิดกะแคชเชียร์ ยิงบาร์โค้ดสินค้า ชั่งน้ำหนัก คิดเงิน รับเงินสด/สแกน QR และพิมพ์รายงานปิดกะ Z-Report</p>
                    <div class="vg-card-steps-count">
                        <i class="bi bi-layers-fill"></i> มีภาพประกอบ 5 ขั้นตอนอย่างละเอียด
                    </div>
                    <button type="button" class="vg-card-btn">
                        <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 2.2 (5 ขั้นตอน)
                    </button>
                </div>
            </div>

            {{-- ==================== บทที่ 3: ระบบสินค้าและคลังสินค้า (Inventory & Warehousing) ==================== --}}
            {{-- Card 3.1: Products Master --}}
            <div class="vg-card" onclick="openManualReader('products')">
                <div class="vg-card-thumb">
                    <img src="{{ asset('images/manual/products_main.png') }}" alt="หน้ารายการสินค้า">
                    <span class="vg-card-badge"><i class="bi bi-camera me-1"></i> บทที่ 3.1: 5 ขั้นตอนพร้อมรูป</span>
                </div>
                <div class="vg-card-body">
                    <h3 class="vg-card-title"><i class="bi bi-box-seam text-danger"></i> 3.1 ทะเบียนสินค้า & สต็อก</h3>
                    <p class="vg-card-desc">คู่มือการเช็คสต็อกแยกตามสาขา การค้นหา การสร้างสินค้าใหม่ การตั้งราคา และการรับเข้าสต็อกด่วน</p>
                    <div class="vg-card-steps-count">
                        <i class="bi bi-layers-fill"></i> มีภาพประกอบ 5 ขั้นตอนอย่างละเอียด
                    </div>
                    <button type="button" class="vg-card-btn">
                        <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 3.1 (5 ขั้นตอน)
                    </button>
                </div>
            </div>

            {{-- Card 3.2: Stock Transfer --}}
            <div class="vg-card" onclick="openManualReader('transfer')">
                <div class="vg-card-thumb">
                    <img src="{{ asset('images/manual/stock_transfers.png') }}" alt="หน้าโอนสต็อก">
                    <span class="vg-card-badge"><i class="bi bi-camera me-1"></i> บทที่ 3.2: 4 ขั้นตอนพร้อมรูป</span>
                </div>
                <div class="vg-card-body">
                    <h3 class="vg-card-title"><i class="bi bi-arrow-left-right text-danger"></i> 3.2 โอนย้ายสต็อกข้ามสาขา</h3>
                    <p class="vg-card-desc">ขั้นตอนการเบิกโอนวัตถุดิบและสินค้าจากคลังใหญ่ (HQ) กระจายสู่สาขาหน้าร้านเพื่อเติมสต็อกให้ขายได้</p>
                    <div class="vg-card-steps-count">
                        <i class="bi bi-layers-fill"></i> มีภาพประกอบ 4 ขั้นตอนอย่างละเอียด
                    </div>
                    <button type="button" class="vg-card-btn">
                        <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 3.2 (4 ขั้นตอน)
                    </button>
                </div>
            </div>

            {{-- Card 3.3: Stock Count --}}
            <div class="vg-card" onclick="openManualReader('counts')">
                <div class="vg-card-thumb">
                    <img src="{{ asset('images/manual/stock_counts.png') }}" alt="หน้าตรวจนับสต็อก">
                    <span class="vg-card-badge"><i class="bi bi-camera me-1"></i> บทที่ 3.3: 4 ขั้นตอนพร้อมรูป</span>
                </div>
                <div class="vg-card-body">
                    <h3 class="vg-card-title"><i class="bi bi-clipboard-check text-danger"></i> 3.3 ตรวจนับสต็อก (Stock Count)</h3>
                    <p class="vg-card-desc">การตรวจนับสินค้าจริงเปรียบเทียบกับระบบ คำนวณผลต่าง (Variance) ของขาด/ของเกิน และการปรับยอดบัญชี</p>
                    <div class="vg-card-steps-count">
                        <i class="bi bi-layers-fill"></i> มีภาพประกอบ 4 ขั้นตอนอย่างละเอียด
                    </div>
                    <button type="button" class="vg-card-btn">
                        <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 3.3 (4 ขั้นตอน)
                    </button>
                </div>
            </div>

            {{-- Card 3.4: Master Data --}}
            <div class="vg-card" onclick="openManualReader('master_data')">
                <div class="vg-card-thumb">
                    <img src="{{ asset('images/manual/master_data_main.png') }}" alt="ข้อมูลหลักและโครงสร้างระบบ">
                    <span class="vg-card-badge" style="background:#0284c7;"><i class="bi bi-people-fill me-1"></i> บทที่ 3.4: 13 ฟังก์ชันพร้อมรูปจริง</span>
                </div>
                <div class="vg-card-body">
                    <h3 class="vg-card-title"><i class="bi bi-people-fill text-info"></i> 3.4 ข้อมูลหลัก & สูตรผลิต BOM (Master Data)</h3>
                    <p class="vg-card-desc">คู่มือศูนย์ตั้งต้นระบบ, ลูกค้า, ซัพพลายเออร์, สมาชิก, ผังคลังสินค้า, ตารางราคา, ราคาเครื่องชั่ง, โปรโมชั่น, ราคานาทีทอง, ป้ายราคา, บัตรส่วนลด, แต้มสะสม และแคมเปญซื้อครบ</p>
                    <div class="vg-card-steps-count">
                        <i class="bi bi-layers-fill"></i> มีภาพประกอบ 13 ฟังก์ชันอย่างละเอียด
                    </div>
                    <button type="button" class="vg-card-btn" style="background:#0284c7;">
                        <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 3.4 (13 ฟังก์ชัน)
                    </button>
                </div>
            </div>

            {{-- ==================== บทที่ 4: ระบบการขายและเอกสารลูกค้า (Sales & Invoicing) ==================== --}}
            {{-- Card 4.1: Sales --}}
            <div class="vg-card" onclick="openManualReader('sales')">
                <div class="vg-card-thumb">
                    <img src="{{ asset('images/manual/sales_main.png') }}" alt="งานขายและเอกสาร">
                    <span class="vg-card-badge" style="background:#d97706;"><i class="bi bi-receipt-cutoff me-1"></i> บทที่ 4.1: 9 ฟังก์ชันพร้อมรูปจริง</span>
                </div>
                <div class="vg-card-body">
                    <h3 class="vg-card-title"><i class="bi bi-receipt-cutoff text-warning"></i> 4.1 งานขาย & ศูนย์เอกสาร (Sales)</h3>
                    <p class="vg-card-desc">คู่มือศูนย์เอกสารย้อนหลัง, ใบเสนอราคา, ใบจอง/ขายเชื่อ, ใบขายสด, ใบรับคืนสินค้า, ใบวางบิล, ใบเพิ่มหนี้/ใบลดหนี้, ลูกค้าสัมพันธ์ (CRM) และ Sales Pipeline Kanban</p>
                    <div class="vg-card-steps-count">
                        <i class="bi bi-layers-fill"></i> มีภาพประกอบ 9 ฟังก์ชันอย่างละเอียด
                    </div>
                    <button type="button" class="vg-card-btn" style="background:#d97706;">
                        <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 4.1 (9 ฟังก์ชัน)
                    </button>
                </div>
            </div>

            {{-- ==================== บทที่ 5: ระบบการจัดซื้อและเจ้าหนี้ (Purchasing & Payables) ==================== --}}
            {{-- Card 5.1: Purchasing & Production --}}
            <div class="vg-card" onclick="openManualReader('purchasing')">
                <div class="vg-card-thumb">
                    <img src="{{ asset('images/manual/purchasing_main.png') }}" alt="จัดซื้อ คลังวัตถุดิบ และการผลิต">
                    <span class="vg-card-badge" style="background:#ea580c;"><i class="bi bi-truck me-1"></i> บทที่ 5.1: 6 ฟังก์ชันพร้อมรูปจริง</span>
                </div>
                <div class="vg-card-body">
                    <h3 class="vg-card-title"><i class="bi bi-truck text-warning"></i> 5.1 จัดซื้อ คลังวัตถุดิบ & การผลิต</h3>
                    <p class="vg-card-desc">คู่มือใบขอซื้อ/สั่งซื้อ (PO), ใบเบิกสินค้า/วัตถุดิบ, ใบคืนสินค้าจากการเบิก, ใบตัดสินค้าชำรุด, สูตรผลิต (BOM)/ใบสั่งผลิต, และแจ้งเตือนสต็อกต่ำจุดสั่งซื้อซ้ำ</p>
                    <div class="vg-card-steps-count">
                        <i class="bi bi-layers-fill"></i> มีภาพประกอบ 6 ฟังก์ชันอย่างละเอียด
                    </div>
                    <button type="button" class="vg-card-btn" style="background:#ea580c;">
                        <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 5.1 (6 ฟังก์ชัน)
                    </button>
                </div>
            </div>

            {{-- ==================== บทที่ 6: ระบบการเงิน บัญชี และภาษี (Finance, Accounting & Tax) ==================== --}}
            {{-- Card 6.1: Finance & Accounting --}}
            <div class="vg-card" onclick="openManualReader('finance')">
                <div class="vg-card-thumb">
                    <img src="{{ asset('images/manual/finance_main.png') }}" alt="ระบบการเงินและบัญชี">
                    <span class="vg-card-badge" style="background:#dc2626;"><i class="bi bi-calculator-fill me-1"></i> บทที่ 6.1: 8 ฟังก์ชันพร้อมรูปจริง</span>
                </div>
                <div class="vg-card-body">
                    <h3 class="vg-card-title"><i class="bi bi-calculator-fill text-danger"></i> 6.1 การเงิน & บัญชี (Finance)</h3>
                    <p class="vg-card-desc">คู่มือสมุดเงินสดรายวัน, ทะเบียนบัญชีธนาคาร, ฝาก/ถอนเงินสด, ทะเบียนเช็ครับ-จ่าย, ผังบัญชีแยกประเภท, สมุดรายวันทั่วไป (GL), งบการเงิน/งบทดลอง และศูนย์ภาษีไทย/E-Tax</p>
                    <div class="vg-card-steps-count">
                        <i class="bi bi-layers-fill"></i> มีภาพประกอบ 8 ฟังก์ชันอย่างละเอียด
                    </div>
                    <button type="button" class="vg-card-btn" style="background:#dc2626;">
                        <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 6.1 (8 ฟังก์ชัน)
                    </button>
                </div>
            </div>

            {{-- ==================== บทที่ 7: รายงานและการวิเคราะห์เชิงบริหาร (Reports & Analytics) ==================== --}}
            {{-- Card 7.1: Reports & Analytics --}}
            <div class="vg-card" onclick="openManualReader('reports')">
                <div class="vg-card-thumb">
                    <img src="{{ asset('images/manual/reports_main.png') }}" alt="รายงานและวิเคราะห์ข้อมูล">
                    <span class="vg-card-badge" style="background:#0891b2;"><i class="bi bi-bar-chart-line-fill me-1"></i> บทที่ 7.1: 6 ฟังก์ชันพร้อมรูปจริง</span>
                </div>
                <div class="vg-card-body">
                    <h3 class="vg-card-title"><i class="bi bi-bar-chart-line-fill text-info"></i> 7.1 รายงาน & วิเคราะห์ข้อมูล (BI)</h3>
                    <p class="vg-card-desc">คู่มือรายงานครบ 6 หมวด: รายงานยอดขาย, รายงานการจัดซื้อและซัพพลายเออร์, รายงานคลังสินค้าและล็อต, รายงานการเงินและการรับชำระ, รายงานภาษีซื้อ/ขาย VAT 7%, และรายงานประวัติระบบเดิม BPlus</p>
                    <div class="vg-card-steps-count">
                        <i class="bi bi-layers-fill"></i> มีภาพประกอบ 6 ฟังก์ชันอย่างละเอียด
                    </div>
                    <button type="button" class="vg-card-btn" style="background:#0891b2;">
                        <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 7.1 (6 ฟังก์ชัน)
                    </button>
                </div>
            </div>

            {{-- ==================== บทที่ 8: การเชื่อมต่อภายนอกและการดูแลระบบ (Integrations & Operations) ==================== --}}
            {{-- Card 8.1: Integrations --}}
            <div class="vg-card" onclick="openManualReader('integrations')">
                <div class="vg-card-thumb">
                    <img src="{{ asset('images/manual/integrations_main.png') }}" alt="เชื่อมต่อภายนอก LINE">
                    <span class="vg-card-badge" style="background:#16a34a;"><i class="bi bi-chat-dots-fill me-1"></i> บทที่ 8.1: 1 ฟังก์ชันพร้อมรูปจริง</span>
                </div>
                <div class="vg-card-body">
                    <h3 class="vg-card-title"><i class="bi bi-chat-dots-fill text-success"></i> 8.1 เชื่อมต่อภายนอก (LINE OA)</h3>
                    <p class="vg-card-desc">คู่มือตั้งค่า LINE Messaging API & LINE Notify สำหรับแจ้งเตือนยอดขายรายวัน แจ้งเตือนสินค้าใกล้หมด และแจ้งเตือนสรุปปิดรอบกะแคชเชียร์อัตโนมัติเข้ากลุ่ม LINE</p>
                    <div class="vg-card-steps-count">
                        <i class="bi bi-layers-fill"></i> มีภาพประกอบ 1 ฟังก์ชันอย่างละเอียด
                    </div>
                    <button type="button" class="vg-card-btn" style="background:#16a34a;">
                        <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 8.1 (1 ฟังก์ชัน)
                    </button>
                </div>
            </div>
        </div>

        {{-- Interactive Manual Reader Modal --}}
        <div id="vgReaderModal" class="vg-modal-backdrop" style="display:none;" onclick="if(event.target === this) closeManualReader()">
            <div class="vg-modal-card">
                <div class="vg-modal-head">
                    <h3 class="vg-modal-title" id="vgModalTitle">
                        <i class="bi bi-book-half"></i> คู่มือการใช้งาน
                    </h3>
                    <div class="d-flex align-items-center gap-2">
                        <a id="vgModalActionBtn" href="#" target="_blank" class="btn btn-sm btn-light fw-bold px-3 rounded-pill text-danger">
                            เปิดโปรแกรมจริง <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                        <button type="button" class="vg-modal-close" onclick="closeManualReader()" aria-label="ปิด">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>

                {{-- Step Tabs Bar --}}
                <div class="vg-step-nav" id="vgStepNav"></div>

                {{-- Body with Picture & Explanation --}}
                <div class="vg-modal-body">
                    <div class="vg-modal-img-wrap" onclick="openVgLightbox()">
                        <img id="vgStepImg" src="" alt="ภาพขั้นตอน">
                        <div class="vg-modal-img-tip"><i class="bi bi-zoom-in me-1"></i> คลิกเพื่อซูมรูปใหญ่</div>
                    </div>
                    <div class="vg-modal-detail">
                        <span class="vg-step-badge" id="vgStepBadge">ขั้นตอนที่ 1</span>
                        <h4 class="vg-step-title" id="vgStepTitle">ชื่อขั้นตอน</h4>
                        <div class="small fw-bold text-danger mt-1 mb-1"><i class="bi bi-bullseye me-1"></i> ลำดับการกดตามกรอบสีแดงในภาพ:</div>
                        <div class="vg-step-desc" id="vgStepDesc">คำอธิบาย</div>
                        
                        <div class="vg-step-tip" id="vgStepTipWrap">
                            <i class="bi bi-lightbulb-fill fs-5 text-warning flex-shrink-0"></i>
                            <div><strong>ข้อควรระวัง / ทริคแนะนำ:</strong> <span id="vgStepTip"></span></div>
                        </div>
                    </div>
                </div>

                {{-- Footer Navigation --}}
                <div class="vg-modal-foot">
                    <button type="button" class="btn btn-sm btn-outline-secondary px-3" id="vgPrevBtn" onclick="prevVgStep()">
                        <i class="bi bi-chevron-left me-1"></i> ขั้นตอนก่อนหน้า
                    </button>
                    <span class="small text-muted fw-bold" id="vgStepCounter">ขั้นตอน 1 จาก 5</span>
                    <button type="button" class="btn btn-sm btn-danger px-4 fw-bold" id="vgNextBtn" onclick="nextVgStep()">
                        ขั้นตอนถัดไป <i class="bi bi-chevron-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Fullscreen Zoom Lightbox --}}
        <div id="vgLightbox" class="vg-lightbox" style="display:none;" onclick="closeVgLightbox()">
            <button type="button" class="vg-lightbox-close" onclick="closeVgLightbox()"><i class="bi bi-x-lg"></i></button>
            <div style="text-align:center" onclick="event.stopPropagation()">
                <img id="vgLightboxImg" src="" alt="ภาพซูมใหญ่">
            </div>
        </div>
    </section>

    <script>
        const manualGuidesData = {
            python_pos: {
                title: 'คู่มือการใช้งานโปรแกรมแคชเชียร์หน้าร้าน (Python POS)',
                route: '{{ route('python-pos.download') }}',
                steps: [
                    {
                        badge: 'ขั้นตอนที่ 1',
                        title: 'เข้าสู่ระบบแคชเชียร์ (Cashier Login)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ชื่อผู้ใช้ / รหัสพนักงาน:</strong> ระบุชื่อผู้ใช้งานของแคชเชียร์ประจำเครื่อง</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>รหัสผ่าน (PIN):</strong> กรอกรหัสผ่านเพื่อยืนยันตัวตนพนักงาน</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่มสีน้ำเงิน "เข้าสู่ระบบ":</strong> หรือกด Enter เพื่อตรวจสอบสิทธิ์เข้าใช้งานโปรแกรม</div></div>`,
                        img: '{{ asset('images/manual/python_pos_step1.png') }}',
                        tip: 'ผู้ใช้งานต้องมีสิทธิ์แคชเชียร์ที่กำหนดไว้ในระบบ ERP จึงจะสามารถเข้าสู่ระบบ POS ได้'
                    },
                    {
                        badge: 'ขั้นตอนที่ 2',
                        title: 'เปิดกะขาย & ใส่เงินทอนเริ่มต้น (Shift Open)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ข้อมูลสาขา & แคชเชียร์:</strong> ตรวจสอบชื่อสาขาและพนักงานผู้รับผิดชอบกะการขาย</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>กรอกเงินทอนตั้งต้น:</strong> ระบุจำนวนเงินสดเริ่มต้นในลิ้นชัก (เช่น 1,000.00 บาท)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่มสีเขียว "🔓 เปิดกะ":</strong> เพื่อเริ่มต้นรอบการขายและเริ่มคิดเงินลูกค้า</div></div>`,
                        img: '{{ asset('images/manual/python_pos_step2.png') }}',
                        tip: 'ยอดเงินทอนตั้งต้นนี้จะถูกนำไปรวมคำนวณกระทบยอดเงินสดเมื่อปิดกะสิ้นวัน'
                    },
                    {
                        badge: 'ขั้นตอนที่ 3',
                        title: 'หน้าต่างขายหลัก & จัดการตะกร้า (Point of Sale)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ยิงบาร์โค้ด / ค้นหา SKU / เลือกหมวดหมู่:</strong> สแกนบาร์โค้ดสินค้า หรือคลิกแท็บหมวดหมู่ด้านบนเพื่อเลือกสินค้าลงบิล</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>รายการในตะกร้า & ยอดรวม:</strong> ตรวจสอบรายการในตารางฝั่งขวา ปรับจำนวน แก้ไข หรือลบรายการที่ไม่ต้องการ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่มสีน้ำเงิน "💳 ชำระเงิน (F12)":</strong> หรือกดปุ่มคีย์ลัด [F12] เพื่อเปิดหน้าต่างรับชำระเงิน</div></div>`,
                        img: '{{ asset('images/manual/python_pos_step3.png') }}',
                        tip: 'สามารถกด F12 บนแป้นพิมพ์เพื่อเปิดหน้าชำระเงินได้ทันทีโดยไม่ต้องคลิกเมาส์'
                    },
                    {
                        badge: 'ขั้นตอนที่ 4',
                        title: 'ชำระเงินด้วยเงินสด & คำนวณเงินทอน (Cash Payment)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>เลือกวิธีชำระ "เงินสด":</strong> ตรวจสอบการ์ดสรุปยอดเงินและจำนวนชิ้นที่ต้องชำระ</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ยอดเงินที่รับ & เงินทอน:</strong> กดแป้นตัวเลขหรือปุ่มด่วน (500, 1,000) ระบบจะแสดงเงินทอนสีเขียวอัตโนมัติ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดยืนยันชำระเงิน (Enter):</strong> บันทึกตัดสต็อก สั่งเปิดลิ้นชักเก็บเงิน และพิมพ์ใบเสร็จอัตโนมัติ</div></div>`,
                        img: '{{ asset('images/manual/python_pos_step4.png') }}',
                        tip: 'หากลูกค้าให้เงินสดพอดี สามารถกดปุ่มจำนวนเงินพอดีเพื่อยืนยันได้รวดเร็ว'
                    },
                    {
                        badge: 'ขั้นตอนที่ 5',
                        title: 'สแกนจ่ายด้วย Thai QR Code (PromptPay)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>กดเลือกแท็บ "QR":</strong> สลับเข้าสู่โหมดรับเงินโอนพร้อมเพย์</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>สแกน QR Code:</strong> ระบบจะสร้าง QR Code พร้อมเพย์ตามยอดบิลให้ลูกค้าสแกนจ่ายผ่านแอปธนาคาร</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดยืนยันชำระเงิน (Enter):</strong> เมื่อตรวจสอบสลิปการโอนเรียบร้อย กดยืนยันเพื่อปิดบิลและพิมพ์ใบเสร็จ</div></div>`,
                        img: '{{ asset('images/manual/python_pos_step5.png') }}',
                        tip: 'หากมีจอแสดงผลฝั่งลูกค้า (Customer Display) QR Code จะขึ้นแสดงบนจอลูกค้าโดยอัตโนมัติ'
                    },
                    {
                        badge: 'ขั้นตอนที่ 6',
                        title: 'ปิดกะทำงานสิ้นสุดวัน (Shift Close & Summary)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ยอดเงินในระบบ:</strong> ตรวจสอบยอดเงินสดที่ระบบคำนวณไว้ (เงินทอนตั้งต้น + ยอดขายเงินสด)</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ยอดเงินนับจริง:</strong> แคชเชียร์นับเงินสดจริงทั้งหมดในลิ้นชักและกรอกลงในช่อง</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่มสีแดง "ยืนยันปิดกะ":</strong> ระบบจะทำการปิดกะ พิมพ์ใบสรุปยอดขาย Z-Report และส่งยอดซิงค์เข้าสู่ระบบ ERP</div></div>`,
                        img: '{{ asset('images/manual/python_pos_step6.png') }}',
                        tip: 'ควรปิดกะทุกครั้งเมื่อเปลี่ยนพนักงานหรือเมื่อสิ้นสุดวันทำการ'
                    }
                ]
            },
            pos: {
                title: 'ระบบขายหน้าร้าน (JET POS)',
                route: '{{ route('pos.index') }}',
                steps: [
                    {
                        badge: 'ขั้นตอนที่ 1',
                        title: 'เปิดกะขาย & ใส่เงินทอนเริ่มต้น',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>กรอกเงินทอนตั้งต้น:</strong> ใส่จำนวนเงินทอนเริ่มต้นในลิ้นชัก (เช่น 1,000 บาท)</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>หมายเหตุเปิดกะ:</strong> ใส่บันทึกเพิ่มเติม (ถ้ามี หรือเว้นว่างไว้ได้)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่มสีเขียว "เปิดกะ":</strong> เพื่อเริ่มบันทึกยอดขายประจำวันและเปิดระบบลิ้นชัก</div></div>`,
                        img: '{{ asset('images/manual/pos_step1.png') }}',
                        tip: 'เงินทอนเริ่มต้นนี้จะถูกนำไปคำนวณกระทบยอดเงินสดเมื่อสิ้นสุดวันในใบรายงาน Z-Report'
                    },
                    {
                        badge: 'ขั้นตอนที่ 2',
                        title: 'เลือกสินค้า / ยิงบาร์โค้ด / ชั่งน้ำหนัก',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ค้นหาสินค้า:</strong> พิมพ์รหัส SKU ชื่อสินค้า หรือใช้เครื่องสแกนยิงบาร์โค้ด</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>เลือกหมวดหมู่สินค้า:</strong> คลิกแท็บหมวดหมู่ด้านบนเพื่อกรองดูเฉพาะกลุ่ม</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>คลิกเลือกสินค้าลงตะกร้า:</strong> กดที่การ์ดสินค้าเพื่อเพิ่มลงตะกร้าขายฝั่งขวา</div></div>`,
                        img: '{{ asset('images/manual/pos_step2.png') }}',
                        tip: 'หากสินค้าขึ้นสต็อกคงเหลือ 0 ระบบจะบล็อกไม่ให้ขายจนกว่าจะมีการทำรายการโอนสต็อกมาจากคลังใหญ่'
                    },
                    {
                        badge: 'ขั้นตอนที่ 3',
                        title: 'จัดการตะกร้าสินค้า & สมาชิกสะสมแต้ม',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>รายการในตะกร้า:</strong> กดปุ่มบวก/ลบ (+/-) เพื่อปรับจำนวน หรือค้นหาเบอร์โทรสะสมแต้มสมาชิก</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "พักบิล":</strong> กดพักบิลชั่วคราวเพื่อสลับไปคิดเงินให้ลูกค้ารายอื่นก่อนได้ทันที</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่มสีแดง "คิดเงิน / ชำระเงิน":</strong> เมื่อลูกค้าพร้อมชำระเงินเพื่อเปิดหน้าต่างชำระเงิน</div></div>`,
                        img: '{{ asset('images/manual/pos_main.png') }}',
                        tip: 'สามารถกดปุ่ม "เรียกบิล" เพื่อดึงบิลที่เคยพักไว้กลับมาคิดเงินต่อได้ตลอดเวลา'
                    },
                    {
                        badge: 'ขั้นตอนที่ 4',
                        title: 'ชำระเงิน & พิมพ์ใบเสร็จรับเงิน',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>เลือกช่องทางชำระเงิน:</strong> เลือกเงินสด, สแกน QR PromptPay, หรือบัตรเครดิต</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ระบุยอดเงิน:</strong> กรอกจำนวนเงินที่รับมา หรือกดปุ่มเงินด่วน (พอดี, 100, 500, 1000)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดยืนยันชำระเงิน:</strong> ระบบจะคำนวณเงินทอน สั่งตัดสต็อก และพิมพ์ใบเสร็จอัตโนมัติ</div></div>`,
                        img: '{{ asset('images/manual/pos_step4.png') }}',
                        tip: 'เครื่องพิมพ์ใบเสร็จและลิ้นชักเก็บเงินจะเปิดออกอัตโนมัติเมื่อกดยืนยันชำระเงิน'
                    },
                    {
                        badge: 'ขั้นตอนที่ 5',
                        title: 'ปิดกะประจำวัน (Z-Report)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ตรวจสอบยอดขาย:</strong> ดูสรุปยอดเงินสดขาย, ยอด QR โอน และยอดเงินสดที่ควรมี</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>นับเงินสดจริงในลิ้นชัก:</strong> กรอกยอดที่นับได้ลงในช่อง "เงินสดที่นับจริง"</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่ม "ปิดกะ":</strong> ระบบจะพิมพ์ใบสรุปยอดขาย Z-Report สำหรับส่งมอบเงินให้ผู้จัดการ</div></div>`,
                        img: '{{ asset('images/manual/pos_step5.png') }}',
                        tip: 'ควรปิดกะทุกสิ้นวัน เพื่อตรวจสอบว่าเงินสดในลิ้นชักตรงกับยอดขายในระบบหรือไม่'
                    }
                ]
            },
            products: {
                title: 'ระบบจัดการสินค้าและราคา (Products Master)',
                route: '{{ route('products.index') }}',
                steps: [
                    {
                        badge: 'ขั้นตอนที่ 1',
                        title: 'ตรวจสอบสต็อก & เลือกดูรายสาขา',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ตัวกรองคลังสินค้า:</strong> เลือกสาขาที่ต้องการดูเพื่อตรวจสอบยอดคงเหลือเฉพาะสาขานั้น</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>การ์ดสรุปภาพรวม:</strong> ดูจำนวนสินค้าทั้งหมด ยอดรวม และการเตือนสต็อกต่ำกว่าเกณฑ์</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางสต็อกรายชิ้น:</strong> ดูสต็อกคงเหลือ ราคาขาย และสถานะการใช้งานของสินค้าแต่ละตัว</div></div>`,
                        img: '{{ asset('images/manual/products_step1.png') }}',
                        tip: 'ตัวเลขสต็อกในตารางตัดจุดทศนิยม .0000 ให้อัตโนมัติเพื่อความอ่านง่าย แต่สำหรับสินค้าชั่งน้ำหนักจะยังคงแสดงเศษทศนิยมตามจริง'
                    },
                    {
                        badge: 'ขั้นตอนที่ 2',
                        title: 'ค้นหาสินค้า & กรองตามประเภท',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ช่องค้นหาสินค้า:</strong> พิมพ์รหัส SKU หรือชื่อสินค้า ระบบจะค้นหาและแสดงผลทันที</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตัวกรองหมวดหมู่:</strong> เลือกประเภทสินค้าเพื่อกรองดูเฉพาะกลุ่มที่สนใจ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ผลลัพธ์การค้นหา:</strong> รายการสินค้าที่ตรงตามคำค้นหาจะแสดงในตารางทันที</div></div>`,
                        img: '{{ asset('images/manual/products_step2.png') }}',
                        tip: 'สามารถกดปุ่มล้าง (x) เพื่อยกเลิกตัวกรองทั้งหมดและกลับสู่รายการสินค้าทั้งหมดได้ตลอดเวลา'
                    },
                    {
                        badge: 'ขั้นตอนที่ 3',
                        title: 'เพิ่มสินค้าใหม่ (+ เพิ่มสินค้า)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>หมวดหมู่ & หน่วยนับ:</strong> เลือกประเภทสินค้า (ระบบรันรหัสอัตโนมัติ) และระบุหน่วยหลัก</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ชื่อสินค้า:</strong> กรอกชื่อภาษาไทยและภาษาอังกฤษให้ชัดเจน</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ราคา & จุดสั่งซื้อ:</strong> กำหนดราคาขายเริ่มต้น คิดภาษี VAT และตั้งจุดเตือนสต็อกสั่งซื้อ</div></div>`,
                        img: '{{ asset('images/manual/products_step3.png') }}',
                        tip: 'หากเลือกหมวดหมู่ ระบบสามารถรันรหัสสินค้า (Auto SKU) ให้อัตโนมัติ'
                    },
                    {
                        badge: 'ขั้นตอนที่ 4',
                        title: 'สินค้าชั่งน้ำหนัก & ทะเบียนเครื่องชั่ง (PLU)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>กรองสินค้าชั่งน้ำหนัก:</strong> เลือกรูปแบบการขายเป็น "สินค้าชั่งน้ำหนัก"</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "ทะเบียนสินค้าเครื่องชั่ง":</strong> กดเพื่อเข้าไปจัดการรหัส PLU เชื่อมกับตู้ชั่งหน้าร้าน</div></div>`,
                        img: '{{ asset('images/manual/products_step4.png') }}',
                        tip: 'เมื่อสินค้าลดลงถึงจุดเตือนสั่งซื้อ ระบบจะแสดงการ์ดแจ้งเตือนสีเหลืองด้านบน'
                    },
                    {
                        badge: 'ขั้นตอนที่ 5',
                        title: 'เพิ่มสินค้า & รับเข้าสต็อกด่วน (Quick Stock-In)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ปุ่ม "+ เพิ่มสินค้า":</strong> กดเพื่อสร้างสินค้าใหม่ที่ยังไม่มีในฐานข้อมูล</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่มรับเข้าด่วน (ไอคอนสีเขียว):</strong> บันทึกรับสินค้าเข้าคลังสาขาได้ทันทีโดยไม่ต้องเปิดใบซื้อ</div></div>`,
                        img: '{{ asset('images/manual/products_main.png') }}',
                        tip: 'การรับเข้าสต็อกด่วนเหมาะสำหรับการปรับยอดรับวัตถุดิบเร่งด่วนหน้าร้าน'
                    }
                ]
            },
            transfer: {
                title: 'การโอนย้ายสต็อกระหว่างสาขา (Stock Transfer)',
                route: '{{ route('stock-transfers.index') }}',
                steps: [
                    {
                        badge: 'ขั้นตอนที่ 1',
                        title: 'รายการใบโอนสต็อก & สถานะ',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ค้นหาเอกสาร:</strong> ค้นหาเลขที่ใบโอนย้ายสต็อกที่ต้องการตรวจสอบ</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ สร้างใบโอนย้าย":</strong> กดเพื่อเปิดใบโอนสินค้าข้ามคลัง/สาขาใบใหม่</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการใบโอน:</strong> ตรวจสอบประวัติการโอน วันที่ คลังปลายทาง และสถานะ</div></div>`,
                        img: '{{ asset('images/manual/transfer_step1.png') }}',
                        tip: 'เอกสารทุกใบจะมีเลขที่รันอัตโนมัติ พร้อมระบุวันที่และผู้จัดทำ'
                    },
                    {
                        badge: 'ขั้นตอนที่ 2',
                        title: 'สร้างใบโอนสินค้าใหม่ (เลือกคลังต้นทาง-ปลายทาง)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>เลือกคลังต้นทางและปลายทาง:</strong> ระบุคลังที่ตัดของออก และคลังสาขาที่ต้องการส่งของไป</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>เลือกสินค้า & ระบุจำนวน:</strong> ค้นหาชื่อสินค้าและระบุจำนวนที่ต้องการโอนย้าย</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่ม "บันทึกใบโอนย้าย":</strong> กดยืนยันบันทึกเพื่อส่งเอกสารไปรออนุมัติ</div></div>`,
                        img: '{{ asset('images/manual/transfer_step2.png') }}',
                        tip: 'คลังต้นทางต้องมียอดสต็อกเพียงพอก่อนทำรายการโอน'
                    },
                    {
                        badge: 'ขั้นตอนที่ 3',
                        title: 'ใบขอเบิกโอนสินค้าจากสาขา (Transfer Request)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>เลือกคลังต้นทาง:</strong> สาขาเลือกคลังใหญ่ที่จะขอเบิกวัตถุดิบมาเติมหน้าร้าน</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>เพิ่มรายการสินค้าที่ขอเบิก:</strong> ใส่สินค้าและจำนวนที่สาขาต้องการขอเติมสต็อก</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่ม "ส่งคำขอ":</strong> ส่งเรื่องให้คลังใหญ่จัดเตรียมสินค้าส่งมอบ</div></div>`,
                        img: '{{ asset('images/manual/transfer_step3.png') }}',
                        tip: 'สามารถเพิ่มสินค้าได้หลายรายการในใบขอเบิกโอนใบเดียวกัน'
                    },
                    {
                        badge: 'ขั้นตอนที่ 4',
                        title: 'อนุมัติ & สาขาปลายทางยืนยันรับเข้า',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ค้นหาและเปิดเอกสาร:</strong> ค้นหาเลขที่ใบโอนที่ส่งมาถึงสาขา</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>กดปุ่มสร้าง/อนุมัติ:</strong> จัดการขั้นตอนการโอนของคลังสินค้า</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ยืนยันรับเข้า:</strong> เมื่อตรวจสอบสินค้าแล้ว กดยืนยันรับเข้าเพื่อเพิ่มสต็อกสาขา</div></div>`,
                        img: '{{ asset('images/manual/transfer_step4.png') }}',
                        tip: 'หากสาขาหน้าร้านติดลบ ให้แอดมินทำรายการโอนนี้เพื่อเติมสต็อกให้สาขาเป็นบวก'
                    }
                ]
            },
            counts: {
                title: 'การตรวจนับสต็อกสินค้า (Stock Count)',
                route: '{{ route('stock-counts.index') }}',
                steps: [
                    {
                        badge: 'ขั้นตอนที่ 1',
                        title: 'เปิดใบตรวจนับสต็อกสินค้าใหม่',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>เลือกสาขาและตำแหน่งเก็บ:</strong> ระบุสาขาและคลังที่ต้องการตรวจนับ</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>เลือกโหมดการนับ:</strong> "นับบางส่วน (Partial)" หรือ "นับเต็ม (Full)"</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่ม "เปิดใบตรวจนับ":</strong> ระบบจะดึงสินค้าทุกตัวในคลังมาเป็นรายการตรวจนับ</div></div>`,
                        img: '{{ asset('images/manual/counts_step1.png') }}',
                        tip: 'ระบบจะดึงสินค้าทุกตัวที่มีสต็อกในตำแหน่งเก็บนั้นมาเป็นรายการตรวจนับ พร้อมยอดตามระบบ ณ ตอนเปิดใบ'
                    },
                    {
                        badge: 'ขั้นตอนที่ 2',
                        title: 'รายการใบตรวจนับสินค้า',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ตารางรายการใบตรวจนับ:</strong> ดูเลขที่ วันที่ สาขา และจำนวนรายการสินค้า</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>สถานะใบตรวจนับ:</strong> ดูสถานะ กำลังนับ / รอตรวจยืนยัน / ปรับปรุงแล้ว</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่ม "กรอกยอดนับ":</strong> เข้าสู่หน้าบันทึกจำนวนที่นับได้จริงเปรียบเทียบกับระบบ</div></div>`,
                        img: '{{ asset('images/manual/counts_step2.png') }}',
                        tip: 'สามารถมีใบนับสต็อกแยกตามสาขาและวันที่ได้อย่างอิสระ'
                    },
                    {
                        badge: 'ขั้นตอนที่ 3',
                        title: 'นับสินค้าจริง & สแกนบาร์โค้ด (Tablet Mode)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>หัวเอกสาร & โหมดตรวจนับ:</strong> ตรวจสอบเลขที่ใบตรวจนับและสาขา</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>กล่องสแกนบาร์โค้ด:</strong> ใช้เครื่องสแกนยิงบาร์โค้ดเพื่อบวกยอดทีละชิ้นอัตโนมัติ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่มนับด่วน & Sync:</strong> กดเพิ่มทีละ +1, +5, +10 หรือนับเป็นลัง แล้วกด Sync ข้อมูล</div></div>`,
                        img: '{{ asset('images/manual/counts_step3.png') }}',
                        tip: 'แนะนำให้นับในช่วงก่อนเปิดร้านหรือหลังปิดร้านเพื่อไม่ให้ยอดขายกระทบ'
                    },
                    {
                        badge: 'ขั้นตอนที่ 4',
                        title: 'ตรวจสอบผลต่าง (Variance) & ปรับปรุงยอดบัญชี',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ค้นหา & ป้ายสรุปผลต่าง:</strong> ดูยอดนับจริง vs ยอดตามระบบ และสรุปส่วนต่าง (Variance)</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางรายการตรวจนับ:</strong> เช็คยอดขาด/ยอดเกิน แยกตามแต่ละรายการสินค้า</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่มบันทึกยอดนับ & จบการนับ:</strong> บันทึกข้อมูลและส่งเรื่องเพื่อปรับปรุงยอดบัญชี</div></div>`,
                        img: '{{ asset('images/manual/counts_step4.png') }}',
                        tip: 'การปรับยอดจะบันทึกเข้า Stock Ledger อย่างโปร่งใสและตรวจสอบย้อนหลังได้'
                    }
                ]
            },
            settings: {
                title: 'คู่มือการตั้งค่าระบบและฟังก์ชันเอกสาร (System & Settings Master Guide)',
                route: '{{ route('settings.index') }}',
                steps: [
                    {
                        badge: 'ฟังก์ชันที่ 1',
                        title: 'ฟังก์ชั่นเอกสาร (ราคารวมภาษี, VAT 7%, วันเครดิต)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ตั้งค่าราคาในเอกสาร:</strong> เลือกว่าราคาสินค้าที่แสดงเป็น "ราคารวมภาษี (Included)" หรือ "ไม่รวมภาษี (Excluded)"</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>อัตราภาษีมูลค่าเพิ่ม (VAT Rate):</strong> กำหนดอัตราร้อยละ (มาตรฐาน 7.00%) ซึ่งระบบจะนำไปคำนวณใน ภ.พ.30 และสมุดรายวัน GL อัตโนมัติ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>เครดิตเริ่มต้น (วัน):</strong> กำหนดจำนวนวันครบกำหนดชำระเงินของลูกหนี้เมื่อเปิดใบขายเชื่อ (เช่น 30 วัน)</div></div>`,
                        img: '{{ asset('images/manual/settings_step1.png') }}',
                        tip: 'เมื่อแก้ไขอัตราภาษีหรือราคา จะมีผลกับเอกสารใหม่ที่สร้างขึ้นตั้งแต่วันนี้เป็นต้นไป โดยไม่กระทบเอกสารเดิมที่บันทึกแล้ว'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 2',
                        title: 'เลขรันเอกสาร (Document Numbering & Sequences)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>รหัสเอกสารนำหน้า (Prefix):</strong> กำหนดตัวอักษรย่อ เช่น INV (ใบแจ้งหนี้), PO (ใบสั่งซื้อ), REC (ใบเสร็จ)</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>รูปแบบปีและเดือน (Format):</strong> เลือก YYYYMM (เช่น 202610) หรือ YYMM เพื่อระบุรอบบัญชีในเลขที่เอกสาร</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>จำนวนหลักและรอบรีเซ็ต:</strong> กำหนดความยาวตัวเลขรัน (เช่น 4 หลัก 0001-9999) และให้รีเซ็ตเริ่ม 0001 ใหม่ทุกต้นเดือน พร้อมปุ่มจัดการสมุดเอกสาร</div></div>`,
                        img: '{{ asset('images/manual/settings_step2.png') }}',
                        tip: 'การจัดรูปแบบเลขที่เอกสารที่ดีช่วยให้ฝ่ายบัญชีและตรวจสอบภายในสามารถสอบทานตามงวดได้อย่างถูกต้อง'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 3',
                        title: 'โลโก้และตราประทับบริษัท (Company Logo & Stamp)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>เลือกโลโก้ระบบ (Presets):</strong> เลือกตราสัญลักษณ์มาตรฐานที่ระบบเตรียมไว้ให้เพื่อความรวดเร็ว</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>อัปโหลดโลโก้และตราประทับใหม่:</strong> อัปโหลดรูปภาพ PNG พื้นหลังโปร่งใส ใช้แสดงบนหัวบิลและตรายางดิจิทัลบนเอกสาร</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่มสีเขียว "💾 บันทึกข้อมูล":</strong> เพื่ออัปเดตรูปภาพเข้าสู่ระบบส่วนกลางและแสดงบนเอกสารทุกประเภททันที</div></div>`,
                        img: '{{ asset('images/manual/settings_step3.png') }}',
                        tip: 'ควรใช้รูปภาพความละเอียดสูง (แนะนำ 600x600 px ขึ้นไป) เพื่อให้พิมพ์ออกมาคมชัดบนเอกสารกระดาษและ PDF'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 4',
                        title: 'หมายเหตุเอกสาร (Document Notes & Footer Text)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ข้อความหมายเหตุท้ายเอกสาร:</strong> กำหนดข้อความที่จะแสดงท้ายใบเสนอราคาและใบแจ้งหนี้อัตโนมัติ เช่น "สินค้าแช่แข็งกรุณาตรวจรับทันทีเมื่อส่งมอบ / ราคานี้ยืน 15 วัน"</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>กดปุ่มสีเขียว "💾 บันทึกข้อมูล":</strong> บันทึกข้อความเพื่อนำไปใช้เป็นค่าเริ่มต้นสำหรับเอกสารฉบับถัดไปทันที</div></div>`,
                        img: '{{ asset('images/manual/settings_step4.png') }}',
                        tip: 'การระบุเงื่อนไขการส่งมอบและอายุราคาชัดเจนช่วยลดข้อพิพาททางการค้ากับลูกค้า'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 5',
                        title: 'ข้อมูลกิจการและผู้เสียภาษี (Company Information)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ชื่อกิจการ (ไทย/อังกฤษ):</strong> กรอกชื่อนิติบุคคลให้ตรงกับหนังสือรับรองกระทรวงพาณิชย์</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>เลขประจำตัวผู้เสียภาษี 13 หลัก:</strong> ระบุเลข 13 หลักและรหัสสาขา (00000 สำหรับสำนักงานใหญ่)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ที่อยู่และช่องทางติดต่อ:</strong> ระบุที่อยู่สถานประกอบการ เบอร์โทร และอีเมล ซึ่งจะถูกนำไปพิมพ์ลงในเอกสารภาษี</div></div>`,
                        img: '{{ asset('images/manual/settings_step5.png') }}',
                        tip: 'ข้อมูลนี้มีความสำคัญทางกฎหมายภาษีอากร ตรวจสอบความถูกต้องให้ตรงกับ ภ.พ.20 ของกิจการ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 6',
                        title: 'ศูนย์ควบคุมโมดูลระบบ (Module Control Center)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>แท็บหมวดหมู่โมดูล:</strong> สลับดูแฟ้มหลัก แฟ้มเสริม และการควบคุมโมดูลแต่ละสายงาน</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>การ์ดสรุปสถานะโมดูล:</strong> แสดงจำนวนโมดูลที่เปิดใช้งานและโมดูลที่ปิดพักไว้</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>สวิตช์เปิด/ปิดโมดูลระบบ:</strong> เปิดใช้โมดูล Kitchen BOM, แปรรูปเนื้อสัตว์ Transform, e-Tax หรือสั่งจองตามความพร้อมของธุรกิจ</div></div>`,
                        img: '{{ asset('images/manual/settings_step6.png') }}',
                        tip: 'การปิดโมดูลจะซ่อนเมนูและระงับการเข้าถึงชั่วคราว แต่ข้อมูลประวัติในอดีตยังคงถูกเก็บรักษาไว้อย่างปลอดภัย 100%'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 7',
                        title: 'POS Designer (ออกแบบผังหน้าจอแคชเชียร์)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ผังหน้าจอ Canvas (12x8 Grid):</strong> ลากและจัดวางตำแหน่งช่องค้นหา, แท็บหมวดหมู่, และรายการการ์ดสินค้าให้เหมาะกับหน้าจอสัมผัส</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ส่วนประกอบ Toolbox:</strong> เลือกดึงวิดเจ็ตตารางตะกร้าขายฝั่งขวา, แป้นตัวเลข, และปุ่มคิดเงินด่วน (F12)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่ม "Build & เผยแพร่ (Publish)":</strong> ส่งผังหน้าจอเวอร์ชันใหม่ไปยังเครื่องแคชเชียร์ POS หน้าร้านทุกสาขา</div></div>`,
                        img: '{{ asset('images/manual/settings_step7.png') }}',
                        tip: 'เมื่อกด Publish เลย์เอาต์เวอร์ชันใหม่ เครื่อง POS หน้าร้านจะรับค่าและปรับหน้าจอตามโดยอัตโนมัติเมื่อทำการ Sync'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 8',
                        title: 'จัดลำดับเมนูระบบ (Menu Section Ordering)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>รายการหมวดหมู่เมนูหลัก:</strong> แสดงลำดับแถบเมนูปัจจุบัน เช่น ภาพรวม, งานประจำวัน, POS, คลัง, การเงิน</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่มเลื่อนขึ้น (▲) และเลื่อนลง (▼):</strong> กดปรับสลับตำแหน่งหมวดหมู่เมนูให้สอดคล้องกับ Workflow ขององค์กร</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่มสีเขียว "💾 บันทึกข้อมูล":</strong> จัดเก็บบันทึกลำดับเมนูใหม่ให้มีผลกับผู้ใช้งานทุกคนในระบบ</div></div>`,
                        img: '{{ asset('images/manual/settings_step8.png') }}',
                        tip: 'การจัดลำดับเมนูที่ใช้บ่อยให้อยู่ด้านบนช่วยลดเวลาในการสลับหน้าจอของพนักงาน'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 9',
                        title: 'ปรับธีมระบบและเลย์เอาต์ (ERP Theme & Layout)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ชุดสีของระบบ (Themes):</strong> เลือกพาเลตต์สีที่สบายตา เช่น ฟ้า JET, กรมท่า, เขียวมรกต, เทาสเลต, หรืออ่านง่าย</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>รูปแบบหน้าหลังบ้าน (Layout):</strong> เลือกระหว่างเลย์เอาต์ "แบบเดิม PopCentral (Sidebar Rail)" หรือ "แบบ Odoo (Topbar Modern)"</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่มสีเขียว "💾 บันทึก Layout":</strong> บันทึกรูปแบบหน้าจอและชุดสีเพื่อปรับใช้อัตโนมัติทันที</div></div>`,
                        img: '{{ asset('images/manual/settings_step9.png') }}',
                        tip: 'การปรับเปลี่ยนธีมและเลย์เอาต์มีผลเฉพาะการแสดงผล UI โดยไม่กระทบต่อข้อมูลขาย สต็อก หรือบัญชีใดๆ ทั้งสิ้น'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 10',
                        title: 'ข้อมูลการรับชำระเงิน (Payments & PromptPay QR)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ข้อมูลบัญชีธนาคาร:</strong> กำหนดธนาคาร เลขที่บัญชี และชื่อบัญชีสำหรับรับโอนเงินค่าสินค้า</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>QR รับชำระ PromptPay:</strong> ผูกเบอร์โทรหรือเลขผู้เสียภาษี 13 หลักสำหรับสร้าง Dynamic QR Code รับเงินผ่านจอ POS</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่มสีเขียว "💾 บันทึกข้อมูล":</strong> ยืนยันการตั้งค่าช่องทางชำระเงินเข้าสู่ระบบแคชเชียร์</div></div>`,
                        img: '{{ asset('images/manual/settings_step10.png') }}',
                        tip: 'Dynamic QR Code ที่สร้างขึ้นจะระบุยอดเงินที่ต้องชำระพอดี ช่วยป้องกันปัญหาลูกค้าสแกนจ่ายเงินขาดหรือเกิน'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 11',
                        title: 'บันทึกบัญชีและผูกผังบัญชี (Default GL Accounts)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ผังบัญชีอัตโนมัติ:</strong> กำหนดรหัสบัญชีที่ระบบใช้ลงรายการอัตโนมัติ เช่น รายได้จากการขาย, ลูกหนี้, เจ้าหนี้, ภาษีซื้อ/ขาย และต้นทุนขาย</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>งบการเงินและการปิดบัญชี:</strong> เชื่อมต่องบดลอง งบกำไรขาดทุน และงบแสดงฐานะการเงินจากรายการบัญชีอัตโนมัติ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่มสีเขียว "💾 บันทึกข้อมูล":</strong> ยืนยันการผูกรหัสบัญชีเข้าสู่ระบบสมุดรายวัน GL</div></div>`,
                        img: '{{ asset('images/manual/settings_step11.png') }}',
                        tip: 'การผูกผังบัญชีที่ถูกต้องจะทำให้ระบบสามารถออกงบการเงิน P&L ได้ทันทีเมื่อปิดกะแคชเชียร์'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 12',
                        title: 'ออกแบบแม่แบบใบเสร็จ POS (Receipt Template Customizer)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ส่วนประกอบหัวสลิป & สาขา:</strong> ลากและวางโลโก้, ชื่อกิจการ, เลขผู้เสียภาษี, และข้อมูลสาขาลงบนแบบใบเสร็จ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ส่วนประกอบท้ายสลิป & ข้อความขอบคุณ:</strong> กำหนดข้อความท้ายใบเสร็จ, สรุปภาษีมูลค่าเพิ่ม, เส้นคั่น, และ QR Code สอบถาม</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>Live Preview ใบเสร็จจริง (80 มม. / 58 มม.):</strong> แสดงผลจำลองสลิปขนาดจริง พร้อมปุ่ม "บันทึกแบบ" ส่งไปยังเครื่องพิมพ์ใบเสร็จความร้อน</div></div>`,
                        img: '{{ asset('images/manual/settings_step12.png') }}',
                        tip: 'สามารถเลือกขนาดกระดาษความร้อน 80 มม. หรือ 58 มม. ให้ตรงกับรุ่นของเครื่องพิมพ์สลิปประจำสาขาได้'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 13',
                        title: 'สายการอนุมัติเอกสารและวงเงิน (Workflows & Approval Limits)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>สายการอนุมัติใบสั่งซื้อ (PO):</strong> กำหนดเพดานวงเงิน เช่น เกิน 50,000 บาท ต้องให้ผู้จัดการฝ่ายจัดซื้ออนุมัติ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>การอนุมัติปรับยอดสต็อก & ใบลดหนี้:</strong> กำหนดให้หัวหน้าคลังหรือผู้จัดการสาขาอนุมัติผ่าน PIN ป้องกันการทุจริต</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่ม "⚙️ บันทึก Workflow":</strong> ปรับเปลี่ยนรายชื่อผู้อนุมัติ ลำดับขั้น และเงื่อนไขตามโครงสร้างองค์กร</div></div>`,
                        img: '{{ asset('images/manual/settings_step13.png') }}',
                        tip: 'เอกสารที่ต้องผ่านการอนุมัติจะมีสถานะเป็น Submitted/Pending Approval จนกว่าผู้มีสิทธิ์จะกดอนุมัติ จึงจะส่งต่อไปตัดสต็อกหรือลงบัญชีได้'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 14',
                        title: 'สำรองข้อมูลและความปลอดภัย (Backup, Security & Database Operations)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>สำรองข้อมูลฉุกเฉิน (Backup Snapshot):</strong> กดปุ่ม "💾 Backup ตอนนี้" เพื่อบีบอัดและสร้างไฟล์สำรองฐานข้อมูลพร้อมรหัสตรวจสอบ SHA-256</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตรวจสอบการกู้คืนและความปลอดภัย (Verify Restore & MFA):</strong> ทดสอบกู้คืนไฟล์สำรองโดยไม่เขียนทับฐานข้อมูลจริง และตรวจสอบสถานะ MFA Session ของผู้ใช้งานในระบบ</div></div>`,
                        img: '{{ asset('images/manual/settings_step14.png') }}',
                        tip: 'การสำรองข้อมูลอย่างสม่ำเสมอเป็นหัวใจสำคัญในการปกป้องข้อมูลทางการเงินและประวัติสต็อกของธุรกิจ'
                    }
                ]
            },
            users: {
                title: 'คู่มือการจัดการผู้ใช้และสิทธิ์การเข้าถึง (User & Security Master Guide)',
                route: '{{ route('users.index') }}',
                steps: [
                    {
                        badge: 'ฟังก์ชันที่ 1',
                        title: 'ค้นหาและกรองสถานะผู้ใช้ (Search & Status Filtering)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ช่องค้นหาผู้ใช้:</strong> พิมพ์รหัสพนักงาน, ชื่อ-นามสกุล, ตำแหน่งงาน หรือเบอร์โทรศัพท์ เพื่อค้นหาผู้ใช้ได้อย่างรวดเร็ว</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>กรองตามสถานะบัญชี:</strong> เลือกดู "เปิดใช้งาน" (Active), "ปิดใช้งาน" (Inactive) หรือ "รอเปลี่ยนรหัสผ่าน" (Must Change Password)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่ม "🔍 ค้นหา" และ "ล้าง":</strong> กดเพื่อประมวลผลการกรอง หรือกดล้างค่าเพื่อแสดงรายชื่อผู้ใช้ทั้งหมด</div></div>`,
                        img: '{{ asset('images/manual/users_step1.png') }}',
                        tip: 'การค้นหาสามารถพิมพ์เฉพาะบางส่วนของชื่อหรือเบอร์โทรศัพท์ได้โดยไม่ต้องกรอกครบทั้งคำ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 2',
                        title: 'แผงควบคุมผู้ใช้สำหรับ POS & การออก PIN (POS Cashier & PIN Issuance)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>การ์ดผู้ใช้ POS:</strong> แสดงรายชื่อแคชเชียร์ที่มีสิทธิ์เข้าใช้งานโปรแกรมหน้าร้าน พร้อมแสดงรหัสสาขาที่สังกัด</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ป้ายสถานะความพร้อม:</strong> แสดง "พร้อมเข้า POS" (สีเขียว) เมื่อมี PIN ถูกต้อง หรือ "ต้องออก/เปลี่ยน PIN" (สีส้ม)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่ม "🔑 ออก PIN POS":</strong> กดเพื่อเปิดหน้าต่างสร้าง PIN ชั่วคราว 6 หลักสำหรับให้แคชเชียร์ล็อกอินหน้าเครื่อง POS</div></div>`,
                        img: '{{ asset('images/manual/users_step2.png') }}',
                        tip: 'PIN POS จะแยกเป็นอิสระจากรหัสผ่านของระบบ ERP เพื่อความสะดวกและรวดเร็วในการสแกนหรือกดแป้นตัวเลขหน้าร้าน'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 3',
                        title: 'แบบฟอร์มเพิ่มผู้ใช้ใหม่ - ข้อมูลทั่วไป (User Profile & Account Info)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ชื่อผู้ใช้ (Username):</strong> กำหนดชื่อบัญชีภาษาอังกฤษสำหรับใช้เข้าสู่ระบบ (ห้ามซ้ำกันในระบบ)</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ชื่อ-นามสกุล (เต็ม):</strong> กรอกชื่อจริงของพนักงาน เพื่อใช้แสดงในเอกสารขาย, บันทึกการอนุมัติ และ Audit Log</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตำแหน่งงาน & ช่องทางติดต่อ:</strong> ระบุตำแหน่ง เช่น แคชเชียร์, ผู้จัดการสาขา พร้อมเบอร์โทรและอีเมล</div></div>`,
                        img: '{{ asset('images/manual/users_step3.png') }}',
                        tip: 'ข้อมูลชื่อ-นามสกุลจะปรากฏบนหัวใบเสร็จรับเงินและลายเซ็นท้ายใบกำกับภาษีอย่างเป็นทางการ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 4',
                        title: 'การกำหนดสาขาและสิทธิ์การเข้าถึง (Branch Access & Sales Territory)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>สาขาหลัก (Primary Branch):</strong> กำหนดสาขาประจำตัวของพนักงาน สำหรับเป็นค่าเริ่มต้นในการเปิดบิลและตัดสต็อก</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>สาขาที่อนุญาตเพิ่มเติม (Multiple Branches):</strong> เลือกสาขาอื่นๆ ที่อนุญาตให้พนักงานเข้าเครื่อง POS หรือทำเอกสารข้ามสาขาได้</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>สายการขาย / สายส่งเริ่มต้น:</strong> ระบุเส้นทางสายส่ง (Sales Area) สำหรับพนักงานขายหน่วยรถ (Van Sales)</div></div>`,
                        img: '{{ asset('images/manual/users_step4.png') }}',
                        tip: 'หากเลือก "ทุกสาขา / ส่วนกลาง" ผู้ใช้จะสามารถดูรายงานและทำเอกสารได้ทุกคลังสินค้าตามระดับสิทธิ์ที่ได้รับ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 5',
                        title: 'การผูกบทบาทและสิทธิ์การใช้งาน (Assign Roles & Password Setup)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ชิปเลือกบทบาท (Role Chips):</strong> ติ๊กเลือกหน้าที่ เช่น แคชเชียร์, คลังสินค้า, ฝ่ายจัดซื้อ, ผู้จัดการสาขา, ผู้ดูแลระบบ</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตั้งรหัสผ่านเริ่มต้น:</strong> กำหนดรหัสผ่านอย่างน้อย 4 ตัวอักษรขึ้นไป พร้อมช่องยืนยันรหัสผ่าน (จัดเก็บเข้ารหัส Bcrypt)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่ม "เพิ่มผู้ใช้" / บันทึกข้อมูล:</strong> กดเพื่อบันทึกข้อมูลพนักงานเข้าสู่ระบบฐานข้อมูลส่วนกลางทันที</div></div>`,
                        img: '{{ asset('images/manual/users_step5.png') }}',
                        tip: 'ผู้ใช้ 1 คนสามารถมีหลายบทบาทพร้อมกันได้ เช่น ติ๊ก "แคชเชียร์" ร่วมกับ "คลังสินค้า" เพื่อให้สามารถขายหน้าร้านและรับโอนของได้ในบัญชีเดียว'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 6',
                        title: 'ตารางรายชื่อผู้ใช้และสถานะสิทธิ์ (User Directory & Security Badges)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>คอลัมน์ผู้ใช้และบทบาท:</strong> แสดง Username, ชื่อ-นามสกุล, สาขา พร้อมป้ายสิทธิ์ย่อย เช่น "เปิด POS", "ขาย POS"</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>เข้าใช้ล่าสุด & สถานะบัญชี:</strong> ตรวจสอบวันเวลาที่ล็อกอินล่าสุด และสถานะ "ใช้งาน" (สีเขียว) หรือ "ปิด" (สีเทา)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่มคำสั่ง "✏️ แก้ไข":</strong> กดเพื่อดึงข้อมูลผู้ใช้ขึ้นไปยังแบบฟอร์มด้านบนเพื่อแก้ไขข้อมูลหรือเปลี่ยนสาขา</div></div>`,
                        img: '{{ asset('images/manual/users_step6.png') }}',
                        tip: 'ตารางรองรับการจัดหน้า (Pagination) เมื่อมีพนักงานจำนวนมาก ทำให้โหลดข้อมูลได้รวดเร็วและเป็นระเบียบ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 7',
                        title: 'การรีเซ็ตรหัสผ่าน ERP และออก PIN POS ใหม่ (Password & PIN Reset Flow)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>หน้าต่างยืนยันรีเซ็ต:</strong> แสดง Username และชื่อพนักงานเพื่อป้องกันการรีเซ็ตผิดบัญชี</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>มาตรฐานความปลอดภัย:</strong> ระบบจะสุ่มรหัสผ่านใหม่หรือ PIN 6 หลักชั่วคราว และบังคับให้พนักงานเปลี่ยนทันทีเมื่อเข้าสู่ระบบ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่ม "🔑 ยืนยัน" & "📋 คัดลอก":</strong> กดยืนยันแล้วระบบจะแสดงรหัสผ่านครั้งเดียว พร้อมปุ่มคัดลอกส่งต่อให้พนักงานอย่างปลอดภัย</div></div>`,
                        img: '{{ asset('images/manual/users_step7.png') }}',
                        tip: 'รหัสผ่านชั่วคราวจะแสดงบนหน้าจอเพียงครั้งเดียวเท่านั้น เพื่อป้องกันไม่ให้บุคคลอื่นแอบดูย้อนหลัง'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 8',
                        title: 'เมทริกซ์บทบาทและสิทธิ์ตามแบบฉบับ BPlus (Roles & Permissions Matrix)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>คอลัมน์บทบาทมาตรฐาน:</strong> แสดงรหัสบทบาทระดับสากล เช่น GM, BRANCH_MGR, ACC_MGR, CASHIER, IT_MGR</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ป้ายสิทธิ์การใช้งาน (Permissions):</strong> แสดงสิทธิ์ย่อยทั้งหมดที่บทบาทนั้นได้รับอย่างละเอียดและโปร่งใส</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>การบังคับสิทธิ์ระดับ Route:</strong> ระบบตรวจสิทธิ์ทุกฟังก์ชัน เช่น pos.use (เปิดจอ), pos.sell (ขาย), pos.void (ยกเลิกบิล) ป้องกันการข้ามสิทธิ์</div></div>`,
                        img: '{{ asset('images/manual/users_step8.png') }}',
                        tip: 'โครงสร้างสิทธิ์ยึดตามมาตรฐาน BPlus เพื่อความปลอดภัยสูงสุดในการควบคุมเงินสดและสต็อกสินค้าของกิจการ'
                    }
                ]
            },
            finance: {
                title: 'คู่มือระบบการเงินและบัญชีครบวงจร (Finance & Accounting)',
                route: '{{ route('monthly-accounting.index') }}',
                steps: [
                    {
                        badge: 'ฟังก์ชันที่ 1',
                        title: 'ปรับปรุงสมุดเงินสด & รายการเงินสดประจำวัน (Cash Books & Adjustments)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>วันที่ สาขา และยอดรับ/จ่ายเงินสด:</strong> ระบุวันที่ทำรายการ, เลือกสาขาที่รับผิดชอบ และกรอกจำนวนเงินสดรับเข้าหรือจ่ายออก</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>คำอธิบาย เหตุผล และผู้อนุมัติ:</strong> ระบุรายการและเหตุผลความจำเป็นในการปรับปรุง พร้อมเลือกผู้มีอำนาจอนุมัติก่อนกดปุ่ม "บันทึก"</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางบัญชีธนาคาร & สมุดเงินสด:</strong> แสดงทะเบียนบัญชีธนาคารพร้อมสถานะ และประวัติรายการเงินสดที่บันทึกไว้ในระบบ</div></div>`,
                        img: '{{ asset('images/manual/finance_step1.png') }}',
                        tip: 'รายการปกติ (ขาย POS, ปิดกะ, ขายสด, รับชำระ, ค่าใช้จ่าย) ระบบเดินเข้าสมุดเงินสดให้อัตโนมัติ — ฟอร์มนี้ใช้เฉพาะรายการปรับปรุงพิเศษที่ต้องมีเหตุผลและผู้อนุมัติกำกับ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 2',
                        title: 'ทะเบียนบัญชีธนาคารบริษัท (Bank Accounts Management)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ช่องค้นหาบัญชีธนาคาร:</strong> พิมพ์ค้นหาจากชื่อธนาคาร หรือเลขที่บัญชีเพื่อตรวจสอบข้อมูลอย่างรวดเร็ว</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ เพิ่มบัญชี":</strong> เปิดหน้าต่างป๊อปอัปเพื่อบันทึกสมุดบัญชีธนาคารเล่มใหม่ ระบุธนาคาร เลขบัญชี ชื่อบัญชี และสาขา</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการบัญชีธนาคาร:</strong> แสดงชื่อธนาคาร, เลขที่บัญชี, ชื่อเจ้าของบัญชี, สาขาสังกัด พร้อมปุ่ม "แก้ไข" ปรับปรุงข้อมูล</div></div>`,
                        img: '{{ asset('images/manual/finance_step2.png') }}',
                        tip: 'บัญชีธนาคารที่ลงทะเบียนไว้จะถูกนำไปใช้อ้างอิงอัตโนมัติในการรับชำระเงินโอน, การออกใบเสร็จรับเงิน และการกระทบยอดสเตทเมนต์'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 3',
                        title: 'บันทึกฝากและถอนเงินสดกับธนาคาร (Cash Transfers Between Cash & Bank)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ประเภทรายการโอน:</strong> เลือกประเภท "ฝากเงินสดเข้าธนาคาร" (เงินออกจากลิ้นชักเข้าแบงก์) หรือ "ถอนเงินสดจากธนาคาร" (นำเงินสดเข้าลิ้นชัก)</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ข้อมูลการโอนเงิน:</strong> ระบุวันที่, จำนวนเงิน, สาขาต้นทาง และเลือกสมุดบัญชีธนาคารปลายทาง</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>การ์ดประวัติรายการล่าสุด:</strong> แสดงประวัติการทำรายการฝาก/ถอนเงินสดย้อนหลัง พร้อมเชื่อมโยงการลงสมุดเงินสดและ GL ให้อัตโนมัติ</div></div>`,
                        img: '{{ asset('images/manual/finance_step3.png') }}',
                        tip: 'เมื่อบันทึกฝากหรือถอนเงินสด ระบบจะตัดยอดสมุดเงินสดและเดบิต/เครดิตบัญชีแยกประเภท (GL) ให้ทันทีโดยไม่ต้องลงบัญชีซ้ำ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 4',
                        title: 'ทะเบียนเช็ครับและเช็คจ่าย (Cheques Register & Status Tracking)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>แท็บสลับ "เช็ครับ" / "เช็คจ่าย":</strong> เลือกระหว่างเช็ครับจากลูกค้า หรือเช็คจ่ายสั่งจ่ายให้เจ้าหนี้/ผู้จำหน่าย</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>การ์ดสรุปสถานะเช็ค 4 กลุ่ม:</strong> ติดตามจำนวนและมูลค่าเช็ค (ในมือ รอนำฝาก, นำฝาก รอผ่าน, ผ่านแล้ว, เช็คคืน/เด้ง) พร้อมระบบแจ้งเตือนเช็คใกล้ครบกำหนด</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่ม "+ บันทึกเช็คเอง" & ฟิลเตอร์:</strong> กรองค้นหาตามเลขที่เช็ค/ธนาคาร/ช่วงวันที่ หรือกดบันทึกเช็คเข้าสู่ทะเบียนโดยตรง</div></div>`,
                        img: '{{ asset('images/manual/finance_step4.png') }}',
                        tip: 'ระบบมีแถบแจ้งเตือนล่วงหน้าเมื่อมีเช็คที่ใกล้ถึงกำหนดนำฝากหรือครบกำหนดตัดบัญชีภายใน 7 วัน เพื่อป้องกันเช็คขาดอายุ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 5',
                        title: 'ผังบัญชีแยกประเภท & บัญชีเริ่มต้นลงบัญชี (Chart of Accounts & Defaults)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ช่องค้นหาผังบัญชี:</strong> ค้นหารหัสบัญชีหรือชื่อบัญชีทั้งภาษาไทยและอังกฤษได้อย่างสะดวก</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "นำเข้า Excel" & "+ เพิ่มบัญชี":</strong> นำเข้าชุดผังบัญชีมาตรฐานผ่านไฟล์ Excel หรือสร้างรหัสบัญชีแยกประเภทใหม่ได้ทันที</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>การ์ดบัญชีเริ่มต้นสำหรับลงบัญชีอัตโนมัติ:</strong> กำหนดคู่บัญชีมาตรฐาน เช่น เงินสด (1010), ธนาคาร (1020), ลูกหนี้ (1030), เจ้าหนี้ (2010), รายได้ (4010), ภาษี (2150)</div></div>`,
                        img: '{{ asset('images/manual/finance_step5.png') }}',
                        tip: 'เมื่อกำหนดบัญชีเริ่มต้นครบ ระบบ ERP จะบันทึกสมุดรายวันทั่วไป (GL) ให้อัตโนมัติทุกครั้งที่มีการเปิดบิลขาย ซื้อสินค้า หรือรับชำระหนี้'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 6',
                        title: 'สมุดรายวันทั่วไป & บันทึกบัญชีแยกประเภท GL (General Journal Entries)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>แท็บสลับ "ผังบัญชี" / "สมุดรายวันทั่วไป":</strong> นำทางระหว่างโครงสร้างผังบัญชีและรายการเดินบัญชีรายวัน</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>การ์ดสรุปยอดเดบิต เครดิต และผลต่าง:</strong> แสดงยอดรวมเดบิต ยอดรวมเครดิต และยืนยันผลต่างต้องเป็น 0.00 เสมอ (ระบบสมดุลคู่)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการลงบัญชีอัตโนมัติ:</strong> แสดงวันที่, เอกสารอ้างอิง, รหัสบัญชี, คำอธิบายรายการ พร้อมยอดเดบิตและเครดิตแยกรายบรรทัด</div></div>`,
                        img: '{{ asset('images/manual/finance_step6.png') }}',
                        tip: 'สมุดรายวันทั่วไปเป็นระบบ Read-Only อัตโนมัติที่โพสต์โดย GlPostingService ทำให้ตัวเลขบัญชีตรงกับเอกสารขาย/ซื้อและ POS แบบ 100%'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 7',
                        title: 'งบการเงินและงบทดลองตรวจสอบยอดดุล (Financial Statements & Trial Balance)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ปุ่มสลับประเภทงบการเงิน:</strong> เลือกดู "งบทดลอง (Trial Balance)", "งบกำไรขาดทุน (P&L)" หรือ "งบแสดงฐานะการเงิน (Balance Sheet)"</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ฟิลเตอร์ช่วงวันที่ & ปุ่มพิมพ์รายงาน:</strong> กำหนดช่วงวันที่ต้องการตรวจสอบ กดปุ่ม "แสดง" เพื่อคำนวณสด หรือกด "พิมพ์" พิมพ์ออกกระดาษ/PDF</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>แผ่นงบทดลอง & สถานะตรวจสอบยอดดุล:</strong> แสดงรหัสบัญชี, ชื่อบัญชี, หมวดหมู่, ยอดรวมเดบิต/เครดิต พร้อมเครื่องหมาย "✓ เดบิตเท่ากับเครดิต — งบดุล"</div></div>`,
                        img: '{{ asset('images/manual/finance_step7.png') }}',
                        tip: 'งบการเงินคำนวณแบบ Real-time จากสมุดรายวันทั่วไป สามารถตรวจสอบสุขภาพการเงิน กำไรสุทธิ และยอดสินทรัพย์คงเหลือได้ทันทีทุกเวลา'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 8',
                        title: 'ศูนย์ภาษีไทยและ E-Tax Invoice (Tax Compliance & Electronic Filing)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ฟิลเตอร์เดือนภาษีและสาขา:</strong> เลือกงวดเดือนภาษีที่ต้องการสรุปยอด และเลือกสาขาหรือ "ทั้งบริษัท" เพื่อรวมศูนย์</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>แท็บแบบภาษี & วงจรจัดทำชุดใหม่:</strong> เลือกประเภทแบบภาษี (ภ.พ.30, ภ.ง.ด.3, ภ.ง.ด.53) และกด "จัดทำชุดใหม่" เพื่อรวมยอดอัตโนมัติ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางแบบภาษี & เวิร์กโฟลว์ 3 ขั้น:</strong> ติดตามขั้นตอน "ผู้จัดทำ (Prepared)" → "ผู้ตรวจ (Reviewed)" → "บันทึกเลขอ้างอิงยื่นจริง (Submitted)" พร้อมดาวน์โหลด CSV และตรวจสอบ SHA-256</div></div>`,
                        img: '{{ asset('images/manual/finance_step8.png') }}',
                        tip: 'ระบบรองรับการสร้างแพ็กเกจ E-Tax Invoice พร้อมสร้าง UUID, Hash SHA-256 และสถานะตอบรับจาก Service Provider ตามเกณฑ์กรมสรรพากร'
                    }
                ]
            },
            sales: {
                title: 'คู่มืองานขายและเอกสารครบวงจร (Sales & Documents)',
                route: '{{ route('documents.browser') }}',
                steps: [
                    {
                        badge: 'ฟังก์ชันที่ 1',
                        title: 'ศูนย์เอกสารย้อนหลัง (Document Browser & History)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>แถบตัวกรองวันที่ & ประเภทเอกสาร:</strong> เลือกช่วงวันที่ (วันเดียวหรือช่วงวัน), เลือกรอบปี/เดือน และเลือกประเภทเอกสารที่ต้องการค้นหา</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางเอกสารย้อนหลัง:</strong> แสดงเลขที่เอกสาร, วันที่, ลูกค้า, ยอดเงินรวม, สถานะเอกสาร และผู้ออกเอกสาร</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่มค้นหากรองข้อมูล:</strong> กดปุ่ม "กรอง" เพื่อเรียกดูข้อมูลตามเงื่อนไข พร้อมปุ่มเปิดดูรายละเอียด พิมพ์ใบเสร็จ หรือพิมพ์ใบกำกับภาษี</div></div>`,
                        img: '{{ asset('images/manual/sales_step1.png') }}',
                        tip: 'ศูนย์เอกสารย้อนหลังเป็นศูนย์กลางค้นหาเอกสารทุกชนิดในระบบ ทั้งบิลขาย POS, ใบขายสด, ใบจอง, ใบวางบิล และใบลดหนี้'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 2',
                        title: 'ใบเสนอราคา (Quotations)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ตัวกรองสถานะใบเสนอราคา:</strong> คัดกรองตามสถานะ (ทุกสถานะ, รอลูกค้าตอบรับ, ตอบรับแล้ว, ปฏิเสธ) พร้อมช่องค้นหาเลขที่หรือชื่อลูกค้า</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ สร้างใบเสนอราคา":</strong> กดปุ่มสีเขียวเพื่อเปิดฟอร์มสร้างใบเสนอราคาใหม่ ระบุลูกค้า สาขา เงื่อนไข และรายการสินค้า</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการใบเสนอราคา:</strong> แสดงเลขที่, วันที่ออก, ชื่อลูกค้า, วันยืนราคาถึง, จำนวนรายการ, ยอดสุทธิ และสถานะตอบรับ</div></div>`,
                        img: '{{ asset('images/manual/sales_step2.png') }}',
                        tip: 'เมื่อลูกค้าตกลงตอบรับใบเสนอราคา สามารถกดแปลงเป็น "ใบจอง / ใบสั่งขาย" ได้ทันทีโดยไม่ต้องคีย์ข้อมูลสินค้าซ้ำ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 3',
                        title: 'ใบจองและขายเชื่อ (Bookings & Credit Sales)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ปุ่ม "+ สร้างใบจอง":</strong> กดปุ่มสีน้ำเงินเพื่อบันทึกการจองสินค้าล่วงหน้า หรือเปิดคำสั่งขายเชื่อแบบให้เครดิตเทอม</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>แถบคัดกรองสมุดและสายส่ง:</strong> เลือกสมุดเอกสาร, สาขา, เขต/สายการขาย และผู้รับผิดชอบใบจองเพื่อติดตามงานขาย</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการใบจอง:</strong> แสดงสถานะใบจอง (รอดำเนินการ, แปลงเป็นใบขายแล้ว), กำหนดส่งมอบ, ยอดรวม และประวัติการจัดส่ง</div></div>`,
                        img: '{{ asset('images/manual/sales_step3.png') }}',
                        tip: 'การเปิดใบจองจะช่วยกันสต็อกไว้ไม่ให้สาขาอื่นขายตัดหน้า และสามารถแปลงเป็นใบขายเชื่อเพื่อตัดสต็อกจริงเมื่อส่งของ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 4',
                        title: 'ใบขายสด (Cash Sales)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ปุ่ม "+ สร้างใบขายสด":</strong> กดปุ่มเพื่อเปิดเอกสารขายสดหน้าร้านหรือขายตรง รับเงินทันทีพร้อมตัดสต็อก</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>โครงสร้างข้อมูลเอกสาร:</strong> ระบุสาขา, ลูกค้า (ทั่วไปหรือเฉพาะราย), วิธีชำระเงิน (เงินสด/โอน), และหมายเหตุ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางประวัติใบขายสด:</strong> แสดงเลขที่ใบเสร็จ, วันที่ขาย, สาขา, ลูกค้า, ยอดเงินรวม และปุ่มพิมพ์ใบเสร็จรับเงิน/ใบกำกับภาษีอย่างย่อ</div></div>`,
                        img: '{{ asset('images/manual/sales_step4.png') }}',
                        tip: 'ใบขายสดบันทึกยอดเงินเข้าสมุดเงินสดอัตโนมัติ และตัดยอดสินค้าออกจากคลังของสาขานั้นทันทีที่ยืนยันเอกสาร'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 5',
                        title: 'ใบรับคืนสินค้า (Sale Returns)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ศูนย์รับคืนสินค้า:</strong> หน้าจอจัดการรับคืนสินค้าจากการขาย ไม่ว่าจะเป็นสินค้าชำรุด เปลี่ยนแปลง หรือลูกค้ายกเลิก</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>โครงสร้างข้อมูลรับคืน:</strong> ระบุเลขอ้างอิงใบขายเดิม, สาขาที่รับสินค้ากลับเข้าคลัง, รายการสินค้า และสาเหตุการรับคืน</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางประวัติเอกสารรับคืน:</strong> ตรวจสอบรายการคืนสินค้า ยอดเงินคืน และการปรับปรุงยอดสต็อกกลับเข้าคลังสินค้า</div></div>`,
                        img: '{{ asset('images/manual/sales_step5.png') }}',
                        tip: 'เมื่ออนุมัติใบรับคืนสินค้า ระบบจะปรับเพิ่มสต็อกกลับเข้าคลังที่ระบุ และสร้างบันทึกทางการเงินเพื่อคืนเงินหรือตั้งยอดเครดิต'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 6',
                        title: 'ใบวางบิล (Billing Notes)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ตัวกรองสถานะใบวางบิล:</strong> คัดกรองตามสถานะ (ทุกสถานะ, รอเก็บเงิน, เก็บเงินแล้ว) เพื่อติดตามลูกหนี้การค้า</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ สร้างใบวางบิล":</strong> กดปุ่มเพื่อเลือกลูกค้าและรวบรวมใบแจ้งหนี้/ใบส่งของที่ค้างชำระมารวมเป็นใบวางบิลฉบับเดียว</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการใบวางบิล:</strong> แสดงเลขที่, วันที่ออก, ลูกค้า, วันครบกำหนดชำระ, ยอดรวม และปุ่มพิมพ์เอกสารใบวางบิลส่งลูกค้า</div></div>`,
                        img: '{{ asset('images/manual/sales_step6.png') }}',
                        tip: 'ใบวางบิลช่วยรวมยอดใบแจ้งหนี้หลายใบของลูกค้าคนเดียวกันในรอบบิล เพื่อให้ลูกค้าชำระเงินรอบเดียวอย่างถูกต้อง'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 7',
                        title: 'ใบเพิ่มหนี้และใบลดหนี้ (Credit & Debit Notes)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>แท็บเลือกใบลดหนี้ / ใบเพิ่มหนี้:</strong> คลิกสลับระหว่างโหมดใบลดหนี้ (CN - ลดภาระหนี้) และใบเพิ่มหนี้ (DN - เพิ่มภาระหนี้)</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ช่องค้นหาและตัวกรอง:</strong> พิมพ์ค้นหาเลขที่เอกสาร, ชื่อลูกค้า หรืออ้างอิงใบกำกับภาษีต้นเรื่อง</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางเอกสาร CN/DN:</strong> แสดงเลขที่, วันที่, ลูกค้า, เลขที่ใบกำกับภาษีเดิมที่อ้างอิง, เหตุผลในการออก และยอดเงินปรับปรุง</div></div>`,
                        img: '{{ asset('images/manual/sales_step7.png') }}',
                        tip: 'การออกใบลดหนี้/เพิ่มหนี้ต้องระบุสาเหตุตามข้อกำหนดสรรพากร (เช่น คำนวณราคาผิด, สินค้าชำรุด) เพื่อให้กระทบภาษีมูลค่าเพิ่มถูกต้อง'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 8',
                        title: 'ลูกค้าสัมพันธ์ (Customer Relationship Management - CRM)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ค้นหาและสรุปสถานะลูกค้า (Customer 360):</strong> ค้นหาประวัติลูกค้าจากชื่อ รหัส หรือเบอร์โทร ดูยอดค้างชำระและสถิติซื้อขาย</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ เปิดงานขาย":</strong> เริ่มต้นเปิดงานขายหรือดีลใหม่กับลูกค้าเพื่อส่งต่อไปยัง Sales Pipeline</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>แบบฟอร์มบันทึกกิจกรรมติดตาม (Activities):</strong> บันทึกการโทรศัพท์, นัดหมายพบปะ, หรือส่งอีเมล พร้อมกำหนดวัน Due Date ติดตามผล</div></div>`,
                        img: '{{ asset('images/manual/sales_step8.png') }}',
                        tip: 'CRM รวมข้อมูลลูกค้า ยอดขายประวัติ และกิจกรรมติดตามไว้ในที่เดียว ช่วยให้ทีมขายบริหารความสัมพันธ์กับลูกค้าได้อย่างมีประสิทธิภาพ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 9',
                        title: 'Pipeline งานขาย (Sales Pipeline Kanban)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ช่องค้นหาโอกาสการขาย:</strong> ค้นหาดีลงานขายตามชื่อลูกค้า หรือหัวข้องานเพื่อคัดกรองในบอร์ด</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ เพิ่มโอกาสการขาย":</strong> เปิดดีลใหม่ ระบุมูลค่าที่คาดหวัง (Expected Amount) ลูกค้า และผู้รับผิดชอบงานขาย</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>บอร์ด Kanban ลำดับขั้นงานขาย:</strong> แสดงการเลื่อนสถานะงานขาย (ลูกค้าใหม่ → ติดต่อแล้ว → สนใจ → ส่งใบเสนอราคา → ปิดการขาย) พร้อมยอดเงินรวมในแต่ละขั้น</div></div>`,
                        img: '{{ asset('images/manual/sales_step9.png') }}',
                        tip: 'Sales Pipeline ช่วยให้ผู้บริหารและทีมขายมองเห็นสถานะงานขายทั้งหมดและคาดการณ์ยอดขายล่วงหน้าได้อย่างแม่นยำ'
                    }
                ]
            },
            master_data: {
                title: 'คู่มือระบบข้อมูลหลักและข้อมูลตั้งต้น (Master Data)',
                route: '{{ route('master-data-setup.index') }}',
                steps: [
                    {
                        badge: 'ฟังก์ชันที่ 1',
                        title: 'ศูนย์ตั้งต้นระบบ & นำเข้าไฟล์ (Master Data Setup Hub & Import)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>การ์ดประเภทสินค้า & สถิติระบบ:</strong> แสดงจำนวนประเภทสินค้าในระบบปัจจุบัน และปุ่มดาวน์โหลด Template สำหรับกำหนดกลุ่ม</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>การ์ดข้อมูลสินค้าหลัก:</strong> ดาวน์โหลด Template สินค้า (รหัส, บาร์โค้ด, ราคา, หน่วยนับ) และอัปโหลดไฟล์นำเข้าข้อมูล</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่ม "ตรวจไฟล์ก่อนนำเข้า":</strong> ตรวจสอบความถูกต้องของโครงสร้างไฟล์และเช็กความซ้ำซ้อนของรหัสสินค้าก่อนบันทึกจริง</div></div>`,
                        img: '{{ asset('images/manual/master_data_step1.png') }}',
                        tip: 'การเตรียมไฟล์ Excel/CSV ควรบันทึกเป็น CSV UTF-8 เพื่อป้องกันปัญหาตัวอักษรภาษาไทยแสดงผลผิดเพี้ยน'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 2',
                        title: 'ลูกค้าและลูกหนี้การค้า (Customers & Debtors Management)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ช่องค้นหาข้อมูลลูกค้า:</strong> ค้นหาจากรหัสลูกค้า, ชื่อร้านค้า, หรือเบอร์โทรศัพท์เพื่อตรวจสอบประวัติได้อย่างรวดเร็ว</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ เพิ่มลูกค้า":</strong> เปิดฟอร์มลงทะเบียนลูกค้าใหม่ กำหนดวงเงินเครดิต เงื่อนไขการชำระเงิน สาขา และสายส่ง</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายชื่อลูกค้า & สถานะหนี้:</strong> แสดงรหัส, ชื่อลูกค้า, สาขา, พนักงานผู้ดูแล, สายการขาย, วงเงินเครดิต และยอดค้างชำระ</div></div>`,
                        img: '{{ asset('images/manual/master_data_step2.png') }}',
                        tip: 'การกำหนดวงเงินเครดิตและระยะเวลาให้สินเชื่อ (Credit Term) ช่วยให้ระบบแจ้งเตือนอัตโนมัติเมื่อลูกค้าสั่งซื้อเกินวงเงินที่อนุมัติ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 3',
                        title: 'ผู้จำหน่ายและเจ้าหนี้การค้า (Suppliers & Creditors Management)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ช่องค้นหาผู้จำหน่าย:</strong> ค้นหาจากรหัสคู่ค้า, ชื่อบริษัท หรือเลขประจำตัวผู้เสียภาษี 13 หลัก</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ เพิ่มซัพพลายเออร์":</strong> บันทึกข้อมูลบริษัทคู่ค้า เลขประจำตัวผู้เสียภาษี ที่อยู่สำนักงานใหญ่/สาขา และบัญชีธนาคารสำหรับจ่ายเงิน</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายชื่อผู้จำหน่าย:</strong> แสดงรหัส, ชื่อคู่ค้า, ยอดหนี้สะสมค้างจ่าย และสถานะการติดต่อสำหรับเปิดใบสั่งซื้อ (PO)</div></div>`,
                        img: '{{ asset('images/manual/master_data_step3.png') }}',
                        tip: 'บันทึกเลขประจำตัวผู้เสียภาษีและสถานะสำนักงานใหญ่/สาขาให้ครบถ้วน เพื่อให้ระบบดึงข้อมูลไปออกใบกำกับภาษีซื้อและรายงานภาษีได้ถูกต้อง'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 4',
                        title: 'ระบบทะเบียนสมาชิก (Members Registry & Tier)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ฟอร์มเพิ่มสมาชิกใหม่:</strong> ระบุรหัสสมาชิก, เบอร์โทรศัพท์, ชื่อ-นามสกุล, และประเภทสมาชิก (เช่น บุคคลทั่วไป, VIP)</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ เพิ่มสมาชิก":</strong> บันทึกสมาชิกใหม่ขึ้นระบบทันที ข้อมูลจะซิงค์ไปยังเครื่องแคชเชียร์ POS ทุกเครื่อง</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการสมาชิก:</strong> แสดงรหัส, ชื่อสมาชิก, ประเภท, ยอดแต้มสะสมปัจจุบัน และสถานะการใช้งาน</div></div>`,
                        img: '{{ asset('images/manual/master_data_step4.png') }}',
                        tip: 'เบอร์โทรศัพท์ของสมาชิกจะถูกใช้เป็นคีย์ลัดที่หน้าเครื่อง POS เพียงพิมพ์เบอร์โทรระบบจะดึงชื่อและแต้มขึ้นจอทันที'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 5',
                        title: 'ผังคลังสินค้าและสถานที่จัดเก็บ (Warehouse Locations & Shelf Bins)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>แถบค้นหาคลังสินค้า:</strong> ค้นหาจากรหัสหรือชื่อคลังสินค้าภายในสาขา</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ เพิ่มคลัง":</strong> กำหนดคลังสินค้าหรือโซนจัดเก็บใหม่ เช่น คลังหน้าร้าน, คลังห้องเย็น, หรือคลังสินค้าชำรุด</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางคลังสินค้า & สถานะ Default:</strong> แสดงรหัสคลัง, ชื่อสถานที่จัดเก็บ, ประเภท และสัญลักษณ์คลังหลักประจำสาขา</div></div>`,
                        img: '{{ asset('images/manual/master_data_step5.png') }}',
                        tip: 'คลังที่ตั้งเป็น DEFAULT จะถูกใช้เป็นคลังตัดสต็อกหลักของบิลขาย POS และใบขายสดโดยอัตโนมัติ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 6',
                        title: 'โครงสร้างตารางราคา (Price Tables & Price Tiers)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ศูนย์จัดการราคาขาย:</strong> แผงควบคุมกลยุทธ์ราคา (ราคาปกติ POS, โปรโมชั่น, นาทีทอง, เครื่องชั่ง) พร้อมสถานะการผูกสาขา</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>แถบค้นหารายการตารางราคา:</strong> ค้นหาตารางราคาขายปลีก ขายส่ง หรือราคาลูกค้าเฉพาะกลุ่ม</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการราคา:</strong> แสดงรหัสตาราง, ชื่อตาราง, คำอธิบาย, จำนวนรายการสินค้า และสถานะการเปิดใช้</div></div>`,
                        img: '{{ asset('images/manual/master_data_step6.png') }}',
                        tip: 'หากสาขาไม่ได้เลือกตารางราคาเฉพาะ ระบบจะใช้ราคามาตรฐานกลางของระบบโดยอัตโนมัติ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 7',
                        title: 'ราคาเครื่องชั่งและพิมพ์บาร์โค้ด (Scale Pricing & Barcodes)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>คำแนะนำสินค้าเครื่องชั่ง (PLU):</strong> ข้อกำหนดช่วงรหัส PLU และขั้นตอนส่งออกข้อมูลเข้าตาชั่งดิจิทัล</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>แถบสถิติสินค้าเครื่องชั่ง:</strong> แสดงจำนวนสินค้าในกลุ่มเครื่องชั่ง (ช่วงเลข PLU นำหน้าด้วย 8)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางจับคู่ PLU & ราคาต่อหน่วย:</strong> แสดงรหัส PLU, รหัสสินค้า, ชื่อสินค้า และราคาต่อกิโลกรัมสำหรับพิมพ์สติกเกอร์</div></div>`,
                        img: '{{ asset('images/manual/master_data_step7.png') }}',
                        tip: 'บาร์โค้ดที่เครื่องชั่งพิมพ์ออกมา (ขึ้นต้นด้วย 8) เครื่องสแกนที่ POS จะถอดรหัสเป็นน้ำหนักและคำนวณราคาให้เองทันที'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 8',
                        title: 'ระบบโปรโมชั่นและส่วนลด (Promotions & Campaign Discounts)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ฟอร์มเพิ่มโปรโมชั่น:</strong> ระบุรหัส, ชื่อโปรโมชั่น, ประเภท (ลดเปอร์เซ็นต์, ลดบาท, คูปอง, ของแถม) และสินค้าที่ร่วมรายการ</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ เพิ่ม":</strong> บันทึกโปรโมชั่นและส่งต่อไปคำนวณอัตโนมัติที่หน้าร้าน</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการโปรโมชั่น:</strong> แสดงช่วงเวลาที่มีผลบังคับใช้ สินค้า เงื่อนไข และสถานะเปิด/ปิด</div></div>`,
                        img: '{{ asset('images/manual/master_data_step8.png') }}',
                        tip: 'สามารถกำหนดวันที่เริ่มต้นและสิ้นสุดของโปรโมชั่นล่วงหน้าได้ เมื่อถึงเวลาโปรโมชั่นจะมีผลทันทีโดยไม่ต้องตั้งค่าใหม่'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 9',
                        title: 'ราคานาทีทอง (Flash Sales & Time-limited Discounts)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ฟอร์มสร้างแคมเปญนาทีทอง:</strong> ระบุรหัส, ชื่อกลุ่มนาทีทอง, สาขาที่ร่วมรายการ, วันที่ และช่วงเวลา (เช่น 18:00 - 19:30)</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ สร้างแคมเปญ":</strong> บันทึกแคมเปญนาทีทองเพื่อเปิดราคาส่วนลดพิเศษเฉพาะช่วงเวลาเร่งด่วน</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการแคมเปญนาทีทอง:</strong> ตรวจสอบประวัติแคมเปญ ช่วงเวลา และสถานะการทำงาน</div></div>`,
                        img: '{{ asset('images/manual/master_data_step9.png') }}',
                        tip: 'เหมาะสำหรับการระบายสินค้าสด เบเกอรี่ หรืออาหารปรุงสำเร็จก่อนเวลาปิดร้าน โดยระบบ POS จะตัดราคาพิเศษตามเวลาจริง'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 10',
                        title: 'ป้ายราคาและพิมพ์ป้ายสินค้า (Price Tags & Shelf Talker)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ปุ่ม "พิมพ์ป้ายราคา":</strong> เลือกสินค้าและส่งพิมพ์ป้ายราคาติดชั้นวาง (Shelf Talkers) ตามขนาดที่ต้องการ</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ฟอร์มเพิ่มรูปแบบป้ายราคา:</strong> กำหนดรหัสป้าย, ชื่อรูปแบบ, แหล่งราคา (ราคาขายปกติ หรือราคาโปรโมชั่น)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรูปแบบป้ายราคา:</strong> แสดงรายการเทมเพลตป้ายราคา ขนาดตัวอักษร และสถานะการใช้งาน</div></div>`,
                        img: '{{ asset('images/manual/master_data_step10.png') }}',
                        tip: 'สามารถเลือกพิมพ์เฉพาะสินค้าที่มีการปรับปรุงราคาใหม่ในสัปดาห์นี้ เพื่อความสะดวกรวดเร็วในการเปลี่ยนป้ายราคาหน้าร้าน'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 11',
                        title: 'บัตรส่วนลดและคูปอง (Discount Cards & Vouchers)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ฟอร์มออกบัตรส่วนลด:</strong> ระบุรหัสบัตร, ชื่อบัตร, ผูกกับสมาชิกเฉพาะราย (ถ้ามี), จำนวนส่วนลด และวันหมดอายุ</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ ออกบัตร":</strong> บันทึกบัตรส่วนลดและสร้างสิทธิ์ใช้งานในระบบทันที</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการบัตรส่วนลด:</strong> ตรวจสอบรหัสบัตร, ชื่อบัตร, สมาชิกผู้ถือบัตร, มูลค่าส่วนลด และยอดใช้งานแล้ว</div></div>`,
                        img: '{{ asset('images/manual/master_data_step11.png') }}',
                        tip: 'บัตรส่วนลดสามารถนำไปสแกนบาร์โค้ดที่หน้าจอชำระเงิน POS เพื่อหักลบยอดบิลได้โดยตรง'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 12',
                        title: 'แต้มสะสมสมาชิก (Member Points & Loyalty Rules)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>การ์ดกติกาแต้มปัจจุบัน:</strong> แสดงอัตราการให้แต้มที่กำลังมีผลใช้งาน (เช่น ซื้อครบ 25 บาท รับ 1 แต้ม)</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ฟอร์มเพิ่มกติกาแต้ม:</strong> กำหนดรหัส, ชื่อกติกา, ประเภท (สะสมแต้มปกติ หรือตัวคูณแต้ม Multiplier), บาทต่อแต้ม และมูลค่าแต้ม</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางกติกาแต้มทั้งหมด:</strong> แสดงประวัติกติกาแต้ม อัตราตัวคูณพิเศษ และสถานะเปิด/ปิดใช้งาน</div></div>`,
                        img: '{{ asset('images/manual/master_data_step12.png') }}',
                        tip: 'สามารถตั้งกติกาแต้มคูณสอง (Multiplier x2) เฉพาะช่วงเทศกาลหรือวันเกิดสมาชิกเพื่อกระตุ้นยอดขายได้'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 13',
                        title: 'แคมเปญซื้อครบจำนวน (Quantity Step Promotions)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ฟอร์มสร้างแคมเปญซื้อครบ:</strong> ระบุรหัส, ชื่อแคมเปญ, ประเภท (ซื้อครบ N ชิ้น แถมฟรี หรือลดพิเศษ), จำนวนขั้นต่ำ และสินค้าที่ร่วมรายการ</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ สร้างแคมเปญ":</strong> บันทึกแคมเปญเพื่อให้ระบบ POS ตรวจสอบจำนวนชิ้นในตะกร้าอัตโนมัติ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการแคมเปญ:</strong> แสดงรหัส, ชื่อแคมเปญ, สินค้า, เงื่อนไขจำนวน และของแถม/ส่วนลดที่ได้รับ</div></div>`,
                        img: '{{ asset('images/manual/master_data_step13.png') }}',
                        tip: 'แคมเปญซื้อครบ (เช่น 1 แถม 1 หรือ 3 ชิ้น 100 บาท) คำนวณแบบ Real-time ที่ POS ช่วยเพิ่มยอดขายต่อบิลได้อย่างดี'
                    }
                ]
            }
,
            purchasing: {
                title: 'คู่มือจัดซื้อ คลังวัตถุดิบ & การผลิต (Purchasing & Production Master Guide)',
                route: '{{ route('purchase-orders.index') }}',
                steps: [
                    {
                        badge: 'ฟังก์ชันที่ 1',
                        title: 'ใบขอซื้อและใบสั่งซื้อ (Purchase Orders - PO)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ปุ่ม "+ สร้างใบขอซื้อ":</strong> กดเพื่อเปิดฟอร์มสร้างเอกสารขอซื้อ/สั่งซื้อใหม่ ระบุซัพพลายเออร์ วันที่ส่งมอบ และรายการสินค้า</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>แผง KPI สรุปสถานะ:</strong> ติดตามจำนวนใบขอซื้อรอดำเนินการ, รออนุมัติ, และยอดสั่งซื้อรวมประจำเดือน</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการใบสั่งซื้อ:</strong> ตรวจสอบประวัติใบสั่งซื้อ เลขที่เอกสาร วันที่ ซัพพลายเออร์ ยอดเงินสุทธิ และสถานะใบสั่งซื้อ</div></div>`,
                        img: '{{ asset('images/manual/purchasing_step1.png') }}',
                        tip: 'เมื่อใบสั่งซื้อผ่านการอนุมัติและรับสินค้าเข้าคลังแล้ว ระบบจะปรับเพิ่มยอดสต็อกและตั้งเจ้าหนี้การค้าอัตโนมัติ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 2',
                        title: 'ใบเบิกสินค้าและวัตถุดิบ (Stock Requisitions)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ปุ่ม "+ สร้างใบเบิกสินค้า":</strong> เปิดฟอร์มเบิกวัตถุดิบไปใช้ในครัว การแปรรูป หรือการใช้อุปโภคบริโภคประจำสาขา</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางประวัติใบเบิก:</strong> ตรวจสอบเลขที่ใบเบิก คลังต้นทาง ผู้เบิก วัตถุประสงค์ และสถานะการเบิกสินค้า</div></div>`,
                        img: '{{ asset('images/manual/purchasing_step2.png') }}',
                        tip: 'การเบิกสินค้าจะบันทึกต้นทุนวัตถุดิบทันทีและตัดยอดคงเหลือออกจากคลังตามวิธีต้นทุนเฉลี่ย'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 3',
                        title: 'ใบคืนสินค้าจากการเบิก (Requisition Returns)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ปุ่ม "+ สร้างใบคืนสินค้าจากการเบิก":</strong> อ้างอิงใบเบิกเดิมเพื่อนำวัตถุดิบหรือสินค้าที่ใช้ไม่หมดส่งคืนเข้าคลัง</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางรายการใบคืนสินค้า:</strong> ตรวจสอบรายการคืนสินค้า จำนวนที่รับคืน และมูลค่าต้นทุนที่ปรับลดลง</div></div>`,
                        img: '{{ asset('images/manual/purchasing_step3.png') }}',
                        tip: 'สินค้าที่รับคืนจะกลับเข้าสต็อกพร้อมขายหรือพร้อมใช้อีกครั้งโดยไม่ต้องตั้งเอกสารรับเข้าใหม่'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 4',
                        title: 'ใบตัดสินค้าชำรุด / เสียหาย (Stock Damages & Write-Offs)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ปุ่ม "+ สร้างใบตัดสินค้าชำรุด":</strong> บันทึกตัดยอดสินค้าแตกหัก เน่าเสีย หมดอายุ หรือสูญหายออกจากคลัง</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางรายการตัดชำรุด:</strong> แสดงรายการสินค้า สาเหตุการชำรุด มูลค่าความเสียหาย และผู้อนุมัติตัดจ่าย</div></div>`,
                        img: '{{ asset('images/manual/purchasing_step4.png') }}',
                        tip: 'การตัดสินค้าชำรุดช่วยให้ตัวเลขสต็อกในระบบตรงกับสินค้าที่มีอยู่จริงบนชั้นวาง ป้องกันแคชเชียร์ขายของที่ไม่มีอยู่'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 5',
                        title: 'สูตรการผลิต (BOM) และใบสั่งผลิต (Production Orders)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ปุ่ม "+ เพิ่มสูตร (BOM)":</strong> กำหนดสูตรการผลิต เช่น ระบุวัตถุดิบ อัตราส่วน และต้นทุนมาตรฐานในการทำสินค้าสำเร็จรูป</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>รายการสูตรการผลิต (Recipes):</strong> ตรวจสอบสูตรทั้งหมด รายการวัตถุดิบย่อย และอัตราสูญเสีย (Yield Loss)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>รายการใบสั่งผลิต (Production Orders):</strong> บันทึกสั่งผลิต ตัดสต็อกวัตถุดิบตามสูตร และเพิ่มสต็อกสินค้าสำเร็จรูป</div></div>`,
                        img: '{{ asset('images/manual/purchasing_step5.png') }}',
                        tip: 'ระบบรองรับการผลิตแบบ Auto-BOM ตัดวัตถุดิบอัตโนมัติเมื่อกดขายหน้าร้าน หรือสั่งผลิตล่วงหน้าเป็นล็อต'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 6',
                        title: 'สินค้าใกล้หมด & จุดสั่งซื้อซ้ำ (Low Stock Alerts & Reorder Points)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ปุ่ม "พิมพ์รายงาน":</strong> พิมพ์รายการสินค้าที่สต็อกต่ำกว่าจุดสั่งซื้อซ้ำ (ROP) เพื่อส่งให้ฝ่ายจัดซื้อเปิด PO</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางแจ้งเตือนสต็อกต่ำ:</strong> แสดงรหัสสินค้า ชื่อ จำนวนคงเหลือ จุดปลอดภัย (Safety Stock) และปริมาณที่แนะนำให้สั่งซื้อ</div></div>`,
                        img: '{{ asset('images/manual/purchasing_step6.png') }}',
                        tip: 'ตั้งค่าจุดสั่งซื้อซ้ำและสต็อกขั้นต่ำได้ในหน้าข้อมูลสินค้า เพื่อให้ระบบแจ้งเตือนก่อนของขาดสต็อกจริง'
                    }
                ]
            },
            reports: {
                title: 'คู่มือรายงานและวิเคราะห์ข้อมูล (Reports & Business Analytics Guide)',
                route: '{{ route('reports.index') }}',
                steps: [
                    {
                        badge: 'ฟังก์ชันที่ 1',
                        title: 'รายงานยอดขายและวิเคราะห์การขาย (Sales Reports & Analytics)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>แท็บหมวดรายงาน & ช่วงวันที่:</strong> เลือกดูยอดขายรายวัน รายเดือน สรุปตามสาขา สินค้าขายดี หรือพนักงานขาย</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางวิเคราะห์ยอดขาย:</strong> แสดงจำนวนบิล ยอดขายรวม ส่วนลด ยอดสุทธิ และกำไรขั้นต้น</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่ม "พิมพ์รายงาน / Export Excel":</strong> พิมพ์เอกสารรายงานหรือดาวน์โหลดเป็นไฟล์สเปรดชีตเพื่อนำไปใช้งานต่อ</div></div>`,
                        img: '{{ asset('images/manual/reports_step1.png') }}',
                        tip: 'รายงานขายสะท้อนข้อมูลแบบ Real-time ทันทีที่มีการปิดบิลที่เครื่อง POS หรือเปิดบิลขายเชื่อหลังบ้าน'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 2',
                        title: 'รายงานการจัดซื้อและเจ้าหนี้ (Purchasing & Supplier Reports)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ตัวกรองการจัดซื้อ:</strong> เลือกช่วงเวลา ซัพพลายเออร์ หมวดหมู่วัตถุดิบ และสถานะการรับของ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางสรุปการซื้อ:</strong> สรุปยอดซื้อสุทธิ ปริมาณการสั่งซื้อ ยอดค้างรับ และประวัติราคาต้นทุนย้อนหลัง</div></div>`,
                        img: '{{ asset('images/manual/reports_step2.png') }}',
                        tip: 'ใช้เปรียบเทียบแนวโน้มราคาวัตถุดิบจากซัพพลายเออร์แต่ละรายเพื่อบริหารต้นทุนให้ต่ำที่สุด'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 3',
                        title: 'รายงานคลังสินค้าและสต็อกเคลื่อนไหว (Inventory Movements & Stock Aging)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ตัวกรองคลังสินค้า:</strong> เลือกคลัง ค้นหารหัสสินค้า หรือตรวจสอบล็อตสินค้าใกล้หมดอายุ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางสต็อกคงเหลือและการเคลื่อนไหว:</strong> แสดงยอดยกมา รับเข้า จ่ายออก โอนย้าย ปรับปรุง และยอดคงเหลือปลายงวด</div></div>`,
                        img: '{{ asset('images/manual/reports_step3.png') }}',
                        tip: 'ตรวจสอบ Stock Card รายตัวเพื่อดูประวัติย้อนหลังทุกธุรกรรมที่ทำให้ยอดสต็อกเปลี่ยนแปลง'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 4',
                        title: 'รายงานการเงินและการรับชำระ (Payment & Cashier Shift Reports)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ตัวกรองประเภทการชำระ:</strong> เลือกช่องทาง เช่น เงินสด, PromptPay QR, บัตรเครดิต, หรือการโอนเงิน</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางสรุปการเงินและรอบกะ:</strong> แสดงยอดเงินรับจริง เปรียบเทียบกับยอดขายหน้าร้าน และสรุปเงินสดขาด/เกิน</div></div>`,
                        img: '{{ asset('images/manual/reports_step4.png') }}',
                        tip: 'รายงานรอบกะช่วยให้ฝ่ายการเงินตรวจสอบยอดเงินนำฝากของแคชเชียร์แต่ละรอบได้อย่างแม่นยำ 100%'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 5',
                        title: 'รายงานภาษีซื้อและภาษีขาย (Input / Output VAT Tax Reports)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ตัวกรองภาษี:</strong> เลือกรายงานภาษีขาย (ภ.พ.30) หรือรายงานภาษีซื้อ กำหนดเดือนและปีภาษี</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางรายงานภาษีมูลค่าเพิ่ม:</strong> แสดงเลขที่ใบกำกับภาษี วันที่ ชื่อผู้ซื้อ/ผู้ขาย เลขผู้เสียภาษี มูลค่าสินค้า และภาษี 7%</div></div>`,
                        img: '{{ asset('images/manual/reports_step5.png') }}',
                        tip: 'รายงานจัดรูปแบบถูกต้องตามข้อกำหนดกรมสรรพากร สามารถนำไปใช้ยื่นแบบ ภ.พ.30 ประจำเดือนได้ทันที'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 6',
                        title: 'รายงานระบบเดิม BPlus (BPlus Legacy Reports)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ศูนย์รวมรายงานระบบเดิม:</strong> เข้าถึงรายงานประวัติเดิมของระบบ BPlus ที่นำเข้าสู่ฐานข้อมูล PopCentral</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางสืบค้นประวัติย้อนหลัง:</strong> ค้นหาบิลเก่า ยอดขายประวัติ และสถิติย้อนหลังหลายปีเพื่อการเปรียบเทียบ</div></div>`,
                        img: '{{ asset('images/manual/reports_step6.png') }}',
                        tip: 'ข้อมูลระบบเดิมถูกจัดเก็บไว้อย่างปลอดภัย สามารถค้นหาย้อนหลังเพื่อตรวจสอบทางบัญชีหรือสรรพากรได้ตลอดเวลา'
                    }
                ]
            },
            integrations: {
                title: 'คู่มือเชื่อมต่อภายนอก (LINE Notify & Messaging API Guide)',
                route: '{{ route('line-integrations.index') }}',
                steps: [
                    {
                        badge: 'ฟังก์ชันที่ 1',
                        title: 'การเชื่อมต่อ LINE Notify และ Messaging API (LINE Integration)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ฟอร์มตั้งค่า Channel Access Token:</strong> กรอก Channel ID, Channel Secret และ Access Token จาก LINE Developers Console</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ เพิ่มกลุ่มแจ้งเตือน" / บันทึก:</strong> ผูกห้องแชตกลุ่มสำหรับส่งข้อความแจ้งเตือนอัตโนมัติ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางเหตุการณ์แจ้งเตือน:</strong> เลือกเปิด/ปิดการแจ้งเตือนตามเหตุการณ์ เช่น สรุปยอดขายประจำวัน, สินค้าสต็อกใกล้หมด, หรือแจ้งเตือนปิดกะแคชเชียร์</div></div>`,
                        img: '{{ asset('images/manual/integrations_step1.png') }}',
                        tip: 'ข้อความที่ส่งเข้า LINE ช่วยให้ผู้บริหารและผู้จัดการสาขารู้สถานะร้านค้าได้แบบ Real-time โดยไม่ต้องเปิดเข้าคอมพิวเตอร์'
                    }
                ]
            },
            overview: {
                title: 'คู่มือภาพรวมและศูนย์ควบคุมบริหาร (Executive Dashboard & Controls Guide)',
                route: '{{ route('dashboard') }}',
                steps: [
                    {
                        badge: 'ฟังก์ชันที่ 1',
                        title: 'แดชบอร์ดภาพรวมผู้บริหาร & ตัวกรองข้อมูล (Executive Board & Smart Filters)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ป้ายหัวข้อ & สถานะเชื่อมต่อสด:</strong> แสดงชื่อระบบ POPSTAR EXECUTIVE BOARD พร้อมไฟสถานะ "ระบบออนไลน์" ยืนยันการเชื่อมต่อฐานข้อมูลแบบ Real-time</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>สรุปยอดขายด่วน & ตัวเลือกสลับสาขา:</strong> ดูยอดขายรวมวันนี้, จำนวนบิลที่เปิดขาย, ยอดแจ้งเตือนคงค้าง และเมนูดรอปดาวน์เลือกดูเฉพาะสาขาหรือรวมทุกสาขาทั่วประเทศ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>แถบตัวกรองช่วงเวลา & Auto-Refresh:</strong> ระบุช่วงวันที่จาก-ถึง, ปุ่มลัด "วันนี้", "7 วัน", "30 วัน" พร้อมตัวเลขนับถอยหลังรีเฟรชข้อมูลอัตโนมัติทุก 60 วินาที</div></div>`,
                        img: '{{ asset('images/manual/overview_step1.png') }}',
                        tip: 'การสลับสาขาใน Dropdown จะอัปเดตทั้งตัวเลขสรุป, กราฟ และตารางสต๊อกของสาขานั้นๆ ทันทีโดยไม่ต้องเปิดหน้ารายงานใหม่'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 2',
                        title: 'แผงแจ้งเตือนด่วน & 4 ดัชนีชี้วัดทางการเงิน (Alert Banner & Financial KPIs Drill-down)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>แถบแจ้งเตือนด่วนสีแดง (Alert Banner):</strong> ตรวจจับ Lot สินค้าใกล้หมดอายุ หรือสต๊อกต่ำ/ติดลบ พร้อมปุ่มกด "ตรวจสอบทันที ➔" เจาะลึกรายงาน</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>KPI รายได้สุทธิ & กำไรขั้นต้น (Gross Profit):</strong> รายได้รวมหลังหักส่วนลด เทียบ % การเติบโตช่วงก่อนหน้า พร้อมคำนวณกำไรขั้นต้นและ Gross Margin % ทันที</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>KPI ลูกหนี้คงค้าง & ยอดขาย POS:</strong> สรุปยอดลูกหนี้ค้างชำระ ยอดเกินกำหนด (Overdue) และยอดขายสดหน้าร้าน POS พร้อมสถานะระบบ POS ที่เชื่อมต่ออยู่</div></div>`,
                        img: '{{ asset('images/manual/overview_step2.png') }}',
                        tip: 'กล่อง KPI ทุกกล่องถูกออกแบบให้เป็นลิงก์ Drill-down สามารถคลิกเพื่อพุ่งตรงเข้าสู่หน้ารายงานเชิงลึกของหมวดนั้นได้ทันที'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 3',
                        title: 'กราฟวิเคราะห์แนวโน้มธุรกิจ (Interactive Analytical Charts)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>กราฟเส้นยอดขายรายวัน (Daily Sales Trend):</strong> แสดงแนวโน้มการเติบโตของรายได้ในแต่ละวัน สามารถนำเมาส์ชี้หรือคลิกจุดบนกราฟเพื่อดูรายละเอียดยอดขายของวันนั้น</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>กราฟแท่งเปรียบเทียบยอดขายแยกสาขา (Branch Sales):</strong> เปรียบเทียบส่วนแบ่งยอดขายของแต่ละสาขาแบบคู่ขนาน คลิกที่แท่งกราฟเพื่อเจาะดูรายงานยอดขายแยกพนักงาน</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กราฟโดนัทสัดส่วนเอกสารขาย (Sales Mix):</strong> แสดงสัดส่วนประเภทการขาย เช่น ยอดบิล POS, เอกสารขายเชื่อ B2B หรือช่องทางออนไลน์ ช่วยให้มองเห็นสัดส่วนช่องทางทำเงินชัดเจน</div></div>`,
                        img: '{{ asset('images/manual/overview_step3.png') }}',
                        tip: 'กราฟวิเคราะห์รองรับการแสดงผลทั้งธีม Light และ Dark Mode โดยปรับเปลี่ยนโทนสีอัตโนมัติเพื่อให้อ่านง่าย สบายตา'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 4',
                        title: 'เฝ้าระวังสินค้าขายดี & สต๊อกวิกฤต (Top 10 Products & Critical Stock Alert)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ตารางสินค้าขายดี Top 10:</strong> แสดงรายการสินค้าที่ทำยอดขายสูงสุด อัปเดตตามรอบบิลขายจริง แสดงรหัส SKU, สาขาที่ขายดี, จำนวนชิ้นที่จำหน่าย และยอดขายรวมเป็นบาท</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางสต๊อกต่ำสุด / ติดลบ:</strong> เฝ้าระวังวัตถุดิบและสินค้าที่คงเหลือต่ำกว่าเกณฑ์ หรือสต๊อกติดลบ แสดงตำแหน่งคลังจัดเก็บ และจำนวนคงเหลือที่ต้องรีบเติมทันที</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ป้ายกำกับเตือน "ตรวจนับด่วน":</strong> ช่วยให้ฝ่ายจัดซื้อและผู้จัดการคลังทราบสินค้าที่กำลังจะขาดมือเพื่อสั่งซื้อหรือโอนย้ายได้ทันท่วงที</div></div>`,
                        img: '{{ asset('images/manual/overview_step4.png') }}',
                        tip: 'หากพบสต๊อกติดลบ ให้ผู้ดูแลสาขาทำรายการตรวจนับสต็อก (Stock Count) หรือรับสินค้าเข้าคลังทันทีเพื่อไม่ให้กระทบยอดขายหน้าร้าน'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 5',
                        title: 'ศูนย์ควบคุมกำไรสุทธิและต้นทุนรายสาขา (Net Profit & Branch Cost Governance)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>แท็บหมวดการควบคุม & ปฏิทินเลือกงวด:</strong> เข้าสู่เมนู "ศูนย์ควบคุมบริหาร" สลับดู 6 แท็บการควบคุม และเลือกงวดบัญชีรายเดือน (Period) เพื่อดูข้อมูลงวดที่ต้องการ</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>4 กล่อง KPI ธรรมาภิบาลต้นทุน:</strong> สรุปยอดขายรวมทั้งเครือ, ต้นทุนขาย (COGS % ของยอดขาย), ค่าใช้จ่ายดำเนินงาน และกำไรสุทธิ (% มาร์จิ้น)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางกำไรหลังต้นทุนและค่าใช้จ่าย:</strong> แจกแจงผลประกอบการแยกตามสาขาแบบละเอียด (สำนักงานใหญ่, หน้าร้าน, ตลาด) พร้อมคำนวณยอดรวมสุทธิทั้งองค์กร</div></div>`,
                        img: '{{ asset('images/manual/overview_step5.png') }}',
                        tip: 'ศูนย์ควบคุมช่วยให้ตรวจจับต้นทุนผิดปกติหรือการรั่วไหลของวัตถุดิบได้ตั้งแต่เนิ่นๆ ก่อนกระทบกำไรสุทธิของกิจการ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 6',
                        title: 'วางแผนสั่งซื้อวัตถุดิบจาก Min/Max (Purchase Planning from Min/Max)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>แท็บ "แผนซื้อ" & ตรวจสอบงวด:</strong> สลับมายังแท็บแผนซื้อเพื่อบริหารคำแนะนำการจัดซื้อวัตถุดิบและสินค้าสำหรับทุกสาขา</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "สร้างคำแนะนำใหม่":</strong> กดเพื่อสั่งให้ระบบคำนวณแผนจัดซื้ออัตโนมัติจากจุด Min/Max, Safety Stock และอัตราการใช้วัตถุดิบจริง</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางแผนจัดซื้อ:</strong> แสดงเลขที่แผน, ชื่อสินค้า/SKU, ซัพพลายเออร์ผู้ขาย, จำนวนชิ้นที่แนะนำให้ซื้อ และสถานะเพื่อนำไปสร้างใบสั่งซื้อ (PO) ต่อไป</div></div>`,
                        img: '{{ asset('images/manual/overview_step6.png') }}',
                        tip: 'ระบบจะดึงซัพพลายเออร์หลักที่ผูกไว้กับสินค้าแต่ละรายการมาใส่ในแผนให้อัตโนมัติ ทำให้ฝ่ายจัดซื้อเปิด PO ได้เร็วขึ้นกว่าเดิม'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 7',
                        title: 'บริหารงบประมาณและศูนย์ต้นทุน (Cost Centers & Budget Governance)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ฟอร์มเพิ่ม Cost Center:</strong> กำหนดรหัสและชื่อศูนย์ต้นทุน เช่น สาขา, แผนกจัดซื้อ, แผนกครัวกลาง, หรือฝ่ายบริหาร เพื่อจัดสรรค่าใช้จ่ายให้ตรงหน่วยงาน</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ฟอร์มตั้งวงเงิน Budget:</strong> กำหนดปีงบประมาณ, ศูนย์ต้นทุน, เดือน, ผังบัญชีค่าใช้จ่าย และวงเงินงบประมาณสูงสุดที่อนุญาตให้ใช้</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตาราง Budget & ปุ่ม "เทียบงบ vs จริง":</strong> ตรวจสอบรายการงบประมาณที่อนุมัติแล้ว พร้อมคลิกดูรายงานเปรียบเทียบการเบิกจ่ายจริงกับวงเงินที่ตั้งไว้</div></div>`,
                        img: '{{ asset('images/manual/overview_step7.png') }}',
                        tip: 'การคุมงบประมาณผ่าน Cost Center ช่วยป้องกันไม่ให้แต่ละแผนกใช้จ่ายเงินเกินเพดานที่ผู้บริหารอนุมัติไว้ในแต่ละงวด'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 8',
                        title: 'บริหารเวลาทำงานและรอบจ่ายเงินเดือน (Attendance & Payroll Governance)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ฟอร์มบันทึกเวลาเข้างาน:</strong> เลือกรหัสพนักงาน, วันที่ปฏิบัติงาน, สถานะ (มาทำงาน, สาย, ลา, ขาด, วันหยุด) และระบุชั่วโมงทำงานล่วงเวลา (OT)</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ระบบประมวลผลรอบเงินเดือน:</strong> เลือกงวดประจำเดือนและกดปุ่ม "คำนวณ Payroll" เพื่อคำนวณฐานเงินเดือน ค่าล่วงเวลา และรายการหักอัตโนมัติ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรอบ Payroll & การอนุมัติ:</strong> สรุปยอดรายได้รวม, หักรวม, ยอดสุทธิ และปุ่ม "ตรวจ / อนุมัติ" เพื่อเปลี่ยนสถานะจาก ร่าง ➔ อนุมัติ ➔ จ่ายแล้ว</div></div>`,
                        img: '{{ asset('images/manual/overview_step8.png') }}',
                        tip: 'ระบบผูกข้อมูลการลงเวลาทำงานเข้ากับการคำนวณ Payroll โดยตรง ช่วยลดความผิดพลาดในการจ่ายค่าแรงและโอที'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 9',
                        title: 'นำเข้าคำสั่งซื้อ E-Commerce & ตรวจสอบระบบ (E-Commerce & Monitoring Governance)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>แถบสลับหมวด E-Commerce & Monitoring:</strong> สลับดูระบบเชื่อมต่อคำสั่งซื้อช่องทางออนไลน์ และแท็บ Monitoring สำหรับตรวจสอบความเสถียรของระบบ</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ฟอร์มนำเข้าคำสั่งซื้อออนไลน์:</strong> บันทึกออเดอร์จากช่องทางภายนอก (Lazada, Shopee, TikTok Shop), ระบุ Order ID, ชื่อลูกค้า, สถานะ, ยอดเงินรวม และรายการสินค้าแบบ JSON</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางคำสั่งซื้อที่นำเข้าแล้ว:</strong> แสดงรายการออเดอร์ออนไลน์ทั้งหมด พร้อมสถานะการตัดสต็อก และความพร้อมในการจัดส่งสินค้าถึงมือลูกค้า</div></div>`,
                        img: '{{ asset('images/manual/overview_step9.png') }}',
                        tip: 'แท็บ Monitoring ช่วยให้ผู้บริหารและฝ่ายไอทีตรวจสอบเหตุการณ์ผิดปกติ (Event Severity) ได้ทันทีเมื่อเกิดข้อผิดพลาดในการเชื่อมต่อ'
                    }
                ]
            }
        };

        let currentGuideKey = 'pos';
        let currentStepIdx = 0;

        function openManualReader(key, stepIndex = 0) {
            if (!manualGuidesData[key]) return;
            currentGuideKey = key;
            currentStepIdx = stepIndex;
            const guide = manualGuidesData[key];

            document.getElementById('vgModalTitle').innerHTML = '<i class="bi bi-book-half me-2"></i>' + guide.title;
            document.getElementById('vgModalActionBtn').href = guide.route || '#';
            document.getElementById('vgModalActionBtn').style.display = guide.route ? 'inline-flex' : 'none';

            // Render step tabs
            const nav = document.getElementById('vgStepNav');
            nav.innerHTML = '';
            guide.steps.forEach((st, idx) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'vg-step-tab' + (idx === currentStepIdx ? ' active' : '');
                btn.innerHTML = `<span class="badge ${idx === currentStepIdx ? 'bg-light text-dark' : 'bg-secondary-subtle text-dark'} rounded-pill me-1">${idx + 1}</span> ${st.title.split('&')[0].trim()}`;
                btn.onclick = () => showVgStep(idx);
                nav.appendChild(btn);
            });

            showVgStep(currentStepIdx);
            document.getElementById('vgReaderModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function showVgStep(idx) {
            const guide = manualGuidesData[currentGuideKey];
            if (!guide || !guide.steps[idx]) return;
            currentStepIdx = idx;
            const step = guide.steps[idx];

            const tabs = document.querySelectorAll('.vg-step-tab');
            tabs.forEach((t, i) => {
                if (i === idx) t.classList.add('active');
                else t.classList.remove('active');
            });

            document.getElementById('vgStepBadge').textContent = step.badge;
            document.getElementById('vgStepTitle').textContent = step.title;
            document.getElementById('vgStepDesc').innerHTML = step.desc;
            document.getElementById('vgStepImg').src = step.img;
            const tipWrap = document.getElementById('vgStepTipWrap');
            if (step.tip) {
                tipWrap.style.display = 'flex';
                document.getElementById('vgStepTip').textContent = step.tip;
            } else {
                tipWrap.style.display = 'none';
            }
            document.getElementById('vgStepCounter').textContent = `ขั้นตอน ${idx + 1} จาก ${guide.steps.length}`;

            document.getElementById('vgPrevBtn').disabled = (idx === 0);
        }

        function prevVgStep() {
            if (currentStepIdx > 0) showVgStep(currentStepIdx - 1);
        }

        function nextVgStep() {
            const guide = manualGuidesData[currentGuideKey];
            if (currentStepIdx < guide.steps.length - 1) {
                showVgStep(currentStepIdx + 1);
            } else {
                closeManualReader();
            }
        }

        function closeManualReader() {
            document.getElementById('vgReaderModal').style.display = 'none';
            document.body.style.overflow = '';
        }

        function openVgLightbox() {
            const src = document.getElementById('vgStepImg').src;
            document.getElementById('vgLightboxImg').src = src;
            document.getElementById('vgLightbox').style.display = 'flex';
        }

        function closeVgLightbox() {
            document.getElementById('vgLightbox').style.display = 'none';
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeVgLightbox();
                closeManualReader();
            }
        });
    </script>

    <section class="manual-panel manual-toolbar">
        <label class="manual-search">
            <i class="bi bi-search"></i>
            <input type="search" x-model="query" placeholder="ค้นหาโปรแกรม ข้อมูลเข้า หรือผลลัพธ์">
        </label>
        <div class="manual-segments" role="tablist" aria-label="หมวด 4M">
            @foreach ($pillars as $pillar)
                <button type="button" class="manual-segment" :class="pillar === '{{ $pillar['key'] }}' && 'active'" @click="pillar = '{{ $pillar['key'] }}'">{{ $pillar['label'] }}</button>
            @endforeach
        </div>
    </section>

    @foreach ($pillars as $pillar)
        <section class="manual-panel" x-show="pillar === '{{ $pillar['key'] }}'" x-cloak>
            <div class="pillar-summary">
                <div class="pillar-icon tone-{{ $pillar['tone'] }}"><i class="bi {{ $pillar['icon'] }}"></i></div>
                <div>
                    <h2 class="pillar-name">{{ $pillar['label'] }} · {{ $pillar['title'] }}</h2>
                    <p class="pillar-copy">{{ $pillar['summary'] }}</p>
                </div>
            </div>
            <div class="manual-table-wrap">
                <table class="manual-table">
                    <thead><tr><th>โปรแกรม</th><th>รับข้อมูลจาก</th><th>ส่งผลไป</th><th class="text-center">เปิด</th></tr></thead>
                    <tbody>
                    @foreach ($pillar['programs'] as $program)
                        <tr x-show="!query || $el.textContent.toLowerCase().includes(query.toLowerCase())">
                            <td><strong>{{ $program[0] }}</strong></td>
                            <td>{{ $program[2] }}</td>
                            <td>{{ $program[3] }}</td>
                            <td class="text-center">
                                @if ($routeAccess($program[1]))
                                    <a class="program-link" href="{{ route($program[1]) }}" title="เปิด {{ $program[0] }}"><i class="bi bi-arrow-up-right"></i></a>
                                @else
                                    <span class="program-locked" title="ไม่มีสิทธิ์หรือยังไม่มีเส้นทาง"><i class="bi bi-lock"></i></span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endforeach

    <section class="manual-panel">
        <div class="manual-section-head">
            <h2><i class="bi bi-bezier2 me-2"></i>เส้นทางข้อมูลของแต่ละวงจร</h2>
            <span>{{ count($workflows) }} วงจรหลัก</span>
        </div>
        <div class="flow-tabs" role="tablist">
            @foreach ($workflows as $workflow)
                <button type="button" class="flow-tab" :class="flow === '{{ $workflow['key'] }}' && 'active'" @click="flow = '{{ $workflow['key'] }}'">{{ $workflow['label'] }}</button>
            @endforeach
        </div>
        @foreach ($workflows as $workflow)
            <div class="flow-content" x-show="flow === '{{ $workflow['key'] }}'" x-cloak>
                <div class="flow-meta">
                    <div><small>ผู้รับผิดชอบ</small><strong>{{ $workflow['owner'] }}</strong></div>
                    <div><small>ผลลัพธ์ที่ต้องได้</small><strong>{{ $workflow['goal'] }}</strong></div>
                </div>
                <div class="flow-steps">
                    @foreach ($workflow['steps'] as $index => $step)
                        <article class="flow-step">
                            @if ($routeAccess($step[1]))<a href="{{ route($step[1]) }}">@endif
                                <span class="flow-number">STEP {{ $index + 1 }}</span>
                                <strong>{{ $step[0] }}</strong>
                                <p>{{ $step[2] }}</p>
                            @if ($routeAccess($step[1]))</a>@endif
                        </article>
                    @endforeach
                </div>
            </div>
        @endforeach
    </section>

    <section class="manual-panel" id="inventory-formulas">
        <div class="manual-section-head">
            <h2><i class="bi bi-calculator me-2"></i>สูตรตัดสต๊อก ต้นทุน และกำไรที่ระบบใช้</h2>
            <span>ตรวจสอบย้อนกลับได้ถึงเอกสารและ Lot</span>
        </div>
        <div class="formula-policy">
            <div><span>ปริมาณ / ต้นทุนต่อหน่วย</span><strong>เก็บ 8 ตำแหน่ง</strong></div>
            <div><span>ยอดเอกสารภายใน</span><strong>เก็บ 4-8 ตำแหน่ง</strong></div>
            <div><span>ยอดชำระ / แสดงผล</span><strong>ปัด Half-up 2 ตำแหน่ง</strong></div>
            <div><span>เกณฑ์ความคลาดเคลื่อน</span><strong>ไม่เกิน 0.00001%</strong></div>
        </div>
        <div class="manual-table-wrap">
            <table class="manual-table formula-table">
                <thead><tr><th>รายการ</th><th>สูตรและตัวอย่าง</th><th>ผลต่อ Stock</th><th>วิธีกระทบยอด</th></tr></thead>
                <tbody>
                @foreach($calculationFormulas as $formula)
                    <tr>
                        <td><strong>{{ $formula['title'] }}</strong></td>
                        <td><code>{{ $formula['formula'] }}</code><small>{{ $formula['example'] }}</small></td>
                        <td>{{ $formula['stock'] }}</td>
                        <td>{{ $formula['check'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="manual-panel" id="blueprint-controls">
        <div class="manual-section-head">
            <h2><i class="bi bi-diagram-3 me-2"></i>BPLUS Rebuild Blueprint: กติกาหลักของระบบใหม่</h2>
            <span>{{ count($blueprintControls) }} control ที่ห้ามหลุด</span>
        </div>
        <div class="coverage-note"><strong>วิธีใช้:</strong> กติกาชุดนี้เป็น Definition of Done ของทุกโมดูล ไม่ใช่แค่เอกสาร POS ถ้าข้อใดตรวจไม่ผ่าน ให้หยุดการ post และเปิดรายการแก้ไขก่อนปิดงวด</div>
        <div class="manual-table-wrap">
            <table class="manual-table" style="min-width:1080px">
                <thead><tr><th>Control</th><th>กติกา</th><th>วิธีตรวจรับ</th></tr></thead>
                <tbody>
                @foreach($blueprintControls as $control)
                    <tr x-show="!query || $el.textContent.toLowerCase().includes(query.toLowerCase())">
                        <td><strong>{{ $control['title'] }}</strong></td>
                        <td>{{ $control['rule'] }}</td>
                        <td>{{ $control['check'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="manual-panel" id="blueprint-roadmap">
        <div class="manual-section-head">
            <h2><i class="bi bi-list-check me-2"></i>ลำดับสร้างระบบตาม BplusBack Blueprint</h2>
            <span>Phase 0–8</span>
        </div>
        <div class="manual-table-wrap">
            <table class="manual-table" style="min-width:1100px">
                <thead><tr><th>ระยะ</th><th>ขอบเขต</th><th>รายละเอียด</th><th>เกณฑ์จบ</th><th>สถานะ</th></tr></thead>
                <tbody>
                @foreach($blueprintRoadmap as $roadmap)
                    <tr x-show="!query || $el.textContent.toLowerCase().includes(query.toLowerCase())">
                        <td><strong>{{ $roadmap['phase'] }}</strong><br>{{ $roadmap['title'] }}</td>
                        <td>{{ $roadmap['scope'] }}</td>
                        <td>ทำตาม flow ใน Blueprint และผูกกับเอกสาร/ledger ที่เกี่ยวข้อง</td>
                        <td>{{ $roadmap['exit'] }}</td>
                        <td><span class="benchmark-status benchmark-{{ $roadmap['tone'] }}">{{ $roadmap['status'] }}</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="manual-panel">
        <div class="manual-section-head">
            <h2><i class="bi bi-clipboard-check me-2"></i>คู่มือทดสอบรับมอบระบบ (UAT)</h2>
            <span>{{ collect($testSuites)->sum(fn ($suite) => count($suite['cases'])) }} กรณี · {{ count($testSuites) }} หมวด</span>
        </div>
        <div class="uat-summary">
            <div><span>กรณีทดสอบทั้งหมด</span><strong x-text="testTotal"></strong></div>
            <div class="pass"><span>ผ่าน</span><strong x-text="testCount('pass')">0</strong></div>
            <div class="fail"><span>ไม่ผ่าน</span><strong x-text="testCount('fail')">0</strong></div>
            <div><span>รอทดสอบ</span><strong x-text="testTotal - testCount('pass') - testCount('fail')"></strong></div>
        </div>
        <div class="uat-policy">
            <div><strong>1. แยกข้อมูลทดสอบ</strong>ใช้สาขา คลัง ผู้ใช้ และเลขเอกสารทดสอบ ห้ามลบข้อมูลจริงเพื่อเตรียมทดสอบ</div>
            <div><strong>2. ทดสอบทั้งผ่านและปฏิเสธ</strong>ทุกจุดควบคุมต้องลองทั้งผู้มีสิทธิ์และผู้ไม่มีสิทธิ์</div>
            <div><strong>3. เก็บหลักฐาน</strong>จดเลขเอกสาร ภาพหน้าจอ รายงาน และผู้ทดสอบทุกกรณีสำคัญ</div>
            <div><strong>4. เกณฑ์ขึ้นใช้งาน</strong>กรณี Critical ต้องผ่านทั้งหมด และกรณีไม่ผ่านต้องมีผู้รับผิดชอบกับวันแก้</div>
        </div>
        <div class="uat-toolbar">
            <label class="manual-search">
                <i class="bi bi-search"></i>
                <input type="search" x-model="testQuery" placeholder="ค้นหารหัสทดสอบ ขั้นตอน หรือผลที่ต้องได้">
            </label>
            <button type="button" class="btn btn-sm btn-outline-secondary" @click="resetTests()"><i class="bi bi-arrow-counterclockwise me-1"></i>ล้างผลในเครื่องนี้</button>
        </div>
        <div class="uat-tabs" role="tablist" aria-label="หมวดทดสอบระบบ">
            @foreach($testSuites as $suite)
                <button type="button" class="uat-tab" :class="testSuite === '{{ $suite['key'] }}' && 'active'" @click="testSuite = '{{ $suite['key'] }}'">{{ $suite['label'] }} ({{ count($suite['cases']) }})</button>
            @endforeach
        </div>
        @foreach($testSuites as $suite)
            <div x-show="testSuite === '{{ $suite['key'] }}'" x-cloak>
                <div class="uat-suite-head">
                    <div><strong>{{ $suite['label'] }}</strong><span>{{ $suite['goal'] }}</span></div>
                    <div class="text-end"><strong>ผู้รับผิดชอบ</strong><span>{{ $suite['owner'] }}</span></div>
                </div>
                @foreach($suite['cases'] as $case)
                    <article class="uat-case" x-show="!testQuery || $el.textContent.toLowerCase().includes(testQuery.toLowerCase())">
                        <div>
                            <div class="uat-case-id">{{ $case[0] }}</div>
                            <span class="uat-risk uat-risk-{{ $case[1] }}">{{ $case[1] === 'critical' ? 'CRITICAL' : 'CONTROL' }}</span>
                        </div>
                        <div>
                            <div class="uat-case-title">{{ $case[2] }}</div>
                            <div class="uat-prepare"><strong>เตรียม:</strong> {{ $case[4] }}</div>
                        </div>
                        <div>
                            <ol class="uat-steps">@foreach($case[5] as $step)<li>{{ $step }}</li>@endforeach</ol>
                            <div class="uat-expected"><strong>ต้องได้:</strong> {{ $case[6] }}</div>
                            <div class="uat-evidence"><strong>หลักฐาน:</strong> {{ $case[7] }}</div>
                        </div>
                        <div class="uat-actions">
                            <button type="button" class="uat-status pass" :class="testResults['{{ $case[0] }}'] === 'pass' && 'active'" @click="setTest('{{ $case[0] }}', 'pass')" title="ผลทดสอบผ่าน"><i class="bi bi-check-lg"></i> ผ่าน</button>
                            <button type="button" class="uat-status fail" :class="testResults['{{ $case[0] }}'] === 'fail' && 'active'" @click="setTest('{{ $case[0] }}', 'fail')" title="ผลทดสอบไม่ผ่าน"><i class="bi bi-x-lg"></i> ไม่ผ่าน</button>
                            <button type="button" class="uat-status pending" :class="(!testResults['{{ $case[0] }}'] || testResults['{{ $case[0] }}'] === 'pending') && 'active'" @click="setTest('{{ $case[0] }}', 'pending')" title="รอทดสอบ"><i class="bi bi-clock"></i> รอ</button>
                            @if($routeAccess($case[3]))
                                <a class="uat-open" href="{{ route($case[3]) }}"><i class="bi bi-box-arrow-up-right"></i>เปิดหน้าทดสอบ</a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endforeach
    </section>

    <section class="manual-panel">
        <div class="manual-section-head">
            <h2><i class="bi bi-clipboard-data me-2"></i>มาตรฐาน ERP ที่ใช้ในประเทศไทย</h2>
            <span>ตรวจข้อมูลล่าสุด 22 ก.ค. 2569</span>
        </div>
        @php
            $benchmarkReady = collect($thaiErpStandards)->where('tone', 'ready')->count();
            $benchmarkPartial = collect($thaiErpStandards)->where('tone', 'partial')->count();
            $benchmarkPlanned = collect($thaiErpStandards)->where('tone', 'planned')->count();
        @endphp
        <div class="benchmark-summary">
            <div><span>พร้อมใช้ใน POPSTAR</span><strong>{{ $benchmarkReady }}</strong></div>
            <div><span>มีแล้วแต่ต้องต่อให้ครบ</span><strong>{{ $benchmarkPartial }}</strong></div>
            <div><span>อยู่ในแผนพัฒนา</span><strong>{{ $benchmarkPlanned }}</strong></div>
        </div>
        <div class="manual-table-wrap">
            <table class="manual-table" style="min-width:1080px">
                <thead><tr><th>หมวด</th><th>ความสามารถมาตรฐาน</th><th>เกณฑ์ใช้งาน</th><th>สถานะ</th><th>งานถัดไป</th><th class="text-center">เปิด</th></tr></thead>
                <tbody>
                @foreach($thaiErpStandards as $standard)
                    <tr x-show="!query || $el.textContent.toLowerCase().includes(query.toLowerCase())">
                        <td>{{ $standard['group'] }}</td>
                        <td><strong>{{ $standard['capability'] }}</strong></td>
                        <td>{{ $standard['benchmark'] }}</td>
                        <td><span class="benchmark-status benchmark-{{ $standard['tone'] }}">{{ $standard['status'] }}</span></td>
                        <td>{{ $standard['next'] }}</td>
                        <td class="text-center">
                            @if($standard['route'] && $routeAccess($standard['route']))
                                <a class="program-link" href="{{ route($standard['route']) }}" title="เปิดโปรแกรม"><i class="bi bi-arrow-up-right"></i></a>
                            @else
                                <span class="program-locked" title="ยังไม่มีโปรแกรมหรือไม่มีสิทธิ์"><i class="bi bi-dash-lg"></i></span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="manual-section-head border-top">
            <h2><i class="bi bi-journal-check me-2"></i>แหล่งอ้างอิงทางการ</h2>
            <span>ใช้วิเคราะห์แนวทาง ไม่คัดลอกซอฟต์แวร์หรือหน้าจอ</span>
        </div>
        <div class="source-list">
            @foreach($thaiErpSources as $source)
                <article class="source-item">
                    <div><strong>{{ $source['name'] }}</strong><span>{{ $source['scope'] }}</span></div>
                    <a class="program-link" href="{{ $source['url'] }}" target="_blank" rel="noopener noreferrer" title="เปิดแหล่งอ้างอิง"><i class="bi bi-box-arrow-up-right"></i></a>
                </article>
            @endforeach
        </div>
    </section>

    <section class="manual-panel" id="bplusback-coverage">
        <div class="manual-section-head">
            <h2><i class="bi bi-arrow-repeat me-2"></i>BplusBack → PopStar 4M: แผนผังความสามารถครบวงจร</h2>
            <span>{{ count($bplusBackCoverage) }} flow ที่ต้องตรวจ</span>
        </div>
        <div class="coverage-note"><strong>หลักการของ PopStar:</strong> ใช้แนวคิดจาก BplusBack แต่ให้เอกสารทุกใบมีสถานะ, ผู้รับผิดชอบ, ผลกระทบ และลิงก์ต้นทาง/ปลายทางชัดเจน ยืนยันแล้วแก้ตรง ๆ ไม่ได้ ต้องกลับรายการ พร้อมเก็บ audit log และไม่แก้ฐานข้อมูล Bplus เดิม</div>
        <div class="manual-table-wrap">
            <table class="manual-table" style="min-width:1260px">
                <thead><tr><th>หมวด</th><th>Flow การทำงาน</th><th>เอกสาร</th><th>ผลกระทบ</th><th>สถานะ</th><th>งานต่อ</th><th class="text-center">เปิด</th></tr></thead>
                <tbody>
                @foreach($bplusBackCoverage as $coverage)
                    <tr x-show="!query || $el.textContent.toLowerCase().includes(query.toLowerCase())">
                        <td><strong>{{ $coverage['group'] }}</strong></td>
                        <td>{{ $coverage['flow'] }}</td>
                        <td>{{ $coverage['documents'] }}</td>
                        <td>{{ $coverage['impact'] }}</td>
                        <td><span class="benchmark-status benchmark-{{ $coverage['tone'] }}">{{ $coverage['status'] }}</span></td>
                        <td>{{ $coverage['next'] }}</td>
                        <td class="text-center">
                            @if($coverage['route'] && $routeAccess($coverage['route']))
                                <a class="program-link" href="{{ route($coverage['route']) }}" title="เปิดโปรแกรม"><i class="bi bi-arrow-up-right"></i></a>
                            @else
                                <span class="program-locked" title="ยังไม่มีโปรแกรมหรือไม่มีสิทธิ์"><i class="bi bi-dash-lg"></i></span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="manual-panel">
        <div class="manual-section-head"><h2><i class="bi bi-shield-check me-2"></i>คู่มือควบคุมระบบ 1-5 แบบละเอียด</h2><span>ตั้งค่า → ปฏิบัติ → ตรวจสอบ → ผลลัพธ์</span></div>
        <div class="control-manuals" x-data="{openControl:'accounting'}">
            @foreach($controlManuals as $manual)
                <article class="control-manual">
                    <button type="button" class="control-title" @click="openControl=openControl==='{{ $manual['key'] }}'?'':'{{ $manual['key'] }}'"><span>{{ $manual['title'] }}</span><i class="bi" :class="openControl==='{{ $manual['key'] }}'?'bi-chevron-up':'bi-chevron-down'"></i></button>
                    <div class="control-body" x-show="openControl==='{{ $manual['key'] }}'" x-cloak>
                        <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap"><div class="flex-grow-1"><div class="control-owner"><strong>ผู้รับผิดชอบ:</strong> {{ $manual['owner'] }}</div><p class="control-purpose"><strong>วัตถุประสงค์:</strong> {{ $manual['purpose'] }}</p></div>@if($routeAccess($manual['route']))<a class="btn btn-sm btn-outline-primary" href="{{ route($manual['route']) }}"><i class="bi bi-box-arrow-up-right me-1"></i>เปิดโปรแกรม</a>@endif</div>
                        <div class="control-grid"><div class="control-block"><h3>เตรียมระบบก่อนใช้</h3><ol>@foreach($manual['setup'] as $item)<li>{{ $item }}</li>@endforeach</ol></div><div class="control-block"><h3>ขั้นตอนปฏิบัติงาน</h3><ol>@foreach($manual['steps'] as $item)<li>{{ $item }}</li>@endforeach</ol></div><div class="control-block"><h3>จุดควบคุม</h3><ul>@foreach($manual['controls'] as $item)<li>{{ $item }}</li>@endforeach</ul></div><div class="control-block"><h3>ผลลัพธ์และหลักฐาน</h3><ul>@foreach($manual['outputs'] as $item)<li>{{ $item }}</li>@endforeach</ul></div></div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <div class="manual-two-col">
        <section class="manual-panel">
            <div class="manual-section-head">
                <h2><i class="bi bi-exclamation-diamond me-2"></i>สิ่งที่ระบบยังขาด</h2>
                <span>จัดลำดับตามความเสี่ยง</span>
            </div>
            <div class="gap-list">
                @foreach ($gaps as $gap)
                    <article class="gap-row">
                        <span class="gap-status gap-{{ $gap['level'] }}">{{ $gap['status'] }}</span>
                        <div><h3>{{ $gap['title'] }}</h3><p>{{ $gap['detail'] }}</p></div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="manual-panel">
            <div class="manual-section-head">
                <h2><i class="bi bi-calendar-check me-2"></i>รอบควบคุมงาน</h2>
                <span>รายการตรวจประจำรอบ</span>
            </div>
            @foreach ($routines as $routine)
                <article class="routine-row">
                    <div class="routine-meta"><strong>{{ $routine['period'] }}</strong><span>{{ $routine['owner'] }}</span></div>
                    <ul class="routine-items">@foreach ($routine['items'] as $item)<li>{{ $item }}</li>@endforeach</ul>
                </article>
            @endforeach
        </section>
    </div>
</div>
@endsection
