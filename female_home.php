<?php
require __DIR__.'/config/config.php';
if(empty($_SESSION['user_id']) || ($_SESSION['gender']??'')!=='female'){ header('Location: dashboard.php'); exit; }
$u=db()->prepare('SELECT * FROM users WHERE id=?'); $u->execute([$_SESSION['user_id']]); $u=$u->fetch();
$site=setting('site_name','Rishtewale');
?>
<!doctype html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=h($site)?></title><link rel="stylesheet" href="assets/style.css"></head><body><main class="phone">
<header><b>💗 <?=h($site)?></b><a href="logout.php">Logout</a></header>
<section class="female-banner">
  <div class="banner-copy"><h1>स्वागत है<br><?=h($u['name'])?> जी 💗</h1><p>आपका रजिस्ट्रेशन सफलतापूर्वक हो गया है।</p></div>
  <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Demo profile" class="banner-girl">
</section>
<section class="home-card-list">
  <a class="home-action" href="dashboard.php"><span class="home-icon">💗</span><span><b>Rishte Dekhe</b><small>अपने शहर के अच्छे लड़कों के profiles देखें</small></span><strong>›</strong></a>
  <a class="home-action earn" href="earning.php"><span class="home-icon">📹</span><span><b><?=h(setting('female_earning_title','Hamare Sath Judkar'))?></b><small><?=h(setting('female_earning_subtitle','30 min Video Calling करके Extra Earning करें'))?></small></span><strong>›</strong></a>
</section>
<section class="how-card"><h2>यह कैसे काम करता है?</h2><p>📹 &nbsp;आप हमारे साथ जुड़कर Video Calling से अच्छी कमाई कर सकती हैं</p><p>🛡️ &nbsp;हमारी टीम आपको Genuine लोगों से Connect कराएगी</p><p>💰 &nbsp;30 मिनट की Video Call पर आपको Earning मिलेगी</p><p>🔒 &nbsp;आपकी Privacy हमारी प्राथमिकता है</p></section>
<nav class="bottom-nav"><a class="active" href="female_home.php">⌂<small>होम</small></a><a href="dashboard.php">♡<small>रिश्ते</small></a><a href="earning.php">▣<small>अर्निंग</small></a><a href="profile.php">♙<small>प्रोफाइल</small></a></nav>
</main><script src="/rishtewale-protection.js"></script></body></html>
