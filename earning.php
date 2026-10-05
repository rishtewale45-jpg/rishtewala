<?php require __DIR__.'/config/config.php'; if(empty($_SESSION['user_id'])||($_SESSION['gender']??'')!=='female'){header('Location:dashboard.php');exit;} $u=db()->prepare('SELECT * FROM users WHERE id=?');$u->execute([$_SESSION['user_id']]);$u=$u->fetch();$site=setting('site_name','Rishtewale'); ?>
<!doctype html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><title>Extra Earning</title><link rel="stylesheet" href="assets/style.css"></head><body><main class="phone">
<header><a href="female_home.php">←</a><b>💗 <?=h($site)?></b><a href="logout.php">Logout</a></header>
<section class="earning-hero"><img src="https://randomuser.me/api/portraits/women/65.jpg" alt="Profile"><div><h1>घर बैठे<br>Video Calling करके<br><b>Extra Earning</b> करें</h1><div class="earning-chip">💰 मात्र 30 मिनट की कॉल के ₹500 - ₹2000 तक कमाएं</div></div></section>
<section class="how-card earning-how"><h2>यह कैसे काम करता है?</h2><p>📹 &nbsp;आप हमारे साथ जुड़कर Video Calling से अच्छी कमाई कर सकती हैं</p><p>🛡️ &nbsp;हमारी टीम आपको Genuine लोगों से Connect कराएगी</p><p>💰 &nbsp;30 मिनट की Video Call पर आपको अच्छी Earning मिलेगी</p><p>🔒 &nbsp;आपकी Privacy हमारी प्राथमिकता है</p></section>
<div class="sticky-apply"><a class="button" href="earning_apply.php">Apply Now</a></div>
</main><script src="/rishtewale-protection.js"></script></body></html>
