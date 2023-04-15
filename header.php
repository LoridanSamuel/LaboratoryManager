<div class="header">
  <input type="checkbox" id="nav--Toggle" class="nav--Toggle" onchange = "bring_nav()">
  <label for="nav--Toggle" class="nav--ToggleLabel">
    <svg viewbox="0 0 100 100" width="100%" height="100%">
      <circle r="45" cx="50" cy="50"></circle>
    </svg>
    <div class="hamburger">
      <div class="center"></div>
    </div>
  </label>
  <div id="dark--ToggleContainer">
    <div class="dark--Toggle">
      <input type="checkbox" name="dark--Toggle" id="dark--Toggle" onchange = "toggleDarkMode()">
      <label class="dark--Label" for="dark--Toggle">
        <div id="dark--Background"></div>
        <span class="face">
          <span class="face-container">
            <span class="eye left"></span>
            <span class="eye right"></span>
            <span class="mouth"></span>
          </span>
        </span>
      </label>
    </div>
  </div>
</div>