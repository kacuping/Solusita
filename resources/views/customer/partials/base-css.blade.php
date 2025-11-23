<style>
  html, body { overflow-x: hidden; height: 100%; }
  body { overscroll-behavior-y: contain; }
  *, *::before, *::after { box-sizing: border-box; }
  .page-spinner-overlay{position:fixed;inset:0;background:rgba(255,255,255,0.75);display:none;align-items:center;justify-content:center;z-index:9999}
  .page-spinner{width:40px;height:40px;border-radius:50%;border:4px solid #dfe6ee;border-top-color:#4b88ff;animation:spin .8s linear infinite}
  @keyframes spin{to{transform:rotate(360deg)}}
</style>
