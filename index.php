<?php require __DIR__.'/config/config.php'; $site=setting('site_name','Rishtewale');
$profiles=[]; try{$q=db()->query("SELECT * FROM profiles WHERE gender='female' AND active=1 ORDER BY RAND() LIMIT 30");$profiles=$q->fetchAll();}catch(Throwable $e){}
if($_SERVER['REQUEST_METHOD']==='POST'){ $gender=$_POST['gender']??'';$age=(int)($_POST['age']??0);$name=trim($_POST['name']??'');$state=trim($_POST['state']??'');$city=trim($_POST['city']??'');$wa=trim($_POST['whatsapp']??'');$tg=trim($_POST['telegram']??''); if($age<18||!in_array($gender,['male','female'],true)||!$name||!$state||!$city||!$wa){$err='Please fill all required details. Age must be 18+';}else{$s=db()->prepare('INSERT INTO users(gender,age,name,state,city,whatsapp,telegram) VALUES(?,?,?,?,?,?,?)');$s->execute([$gender,$age,$name,$state,$city,$wa,$tg]);$_SESSION['user_id']=db()->lastInsertId();$_SESSION['gender']=$gender;$_SESSION['city']=$city;$_SESSION['state']=$state;header('Location: '.($gender==='female'?'female_home.php':'dashboard.php'));exit;}}
?>
<!doctype html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=h($site)?></title><link rel="stylesheet" href="assets/style.css"></head><body><main class="phone"><header><b>💗 <?=h($site)?></b><span>● Online</span></header><section class="hero home-hero" id="register">
<div class="hero-slider" data-interval="<?=h(setting('home_slider_interval','4000'))?>">
<?php
$bannerDefaults=[
 1=>['active'=>'1','title'=>'Apna behtareen rishta dhoondhiye','subtitle'=>'Hazaron verified profiles mein apna perfect match paaiye','image'=>'https://randomuser.me/api/portraits/women/44.jpg','button'=>'Abhi Register Karein','link'=>'#register'],
 2=>['active'=>'1','title'=>'Aapke shehar mein naye profiles','subtitle'=>'Online profiles dekhiye aur apni pasand ka rishta chuniye','image'=>'https://randomuser.me/api/portraits/women/68.jpg','button'=>'Profiles Dekhein','link'=>'#register'],
 3=>['active'=>'1','title'=>'Safe, Simple & Private','subtitle'=>'Apni details bhar kar matching profiles se judiye','image'=>'https://randomuser.me/api/portraits/women/79.jpg','button'=>'Join Now','link'=>'#register']
];
$slides=[];
for($i=1;$i<=3;$i++){
  if(setting("home_banner_{$i}_active",$bannerDefaults[$i]['active'])!=='1') continue;
  $slides[]=[
    'title'=>setting("home_banner_{$i}_title",$bannerDefaults[$i]['title']),
    'subtitle'=>setting("home_banner_{$i}_subtitle",$bannerDefaults[$i]['subtitle']),
    'image'=>setting("home_banner_{$i}_image",$bannerDefaults[$i]['image']),
    'button'=>setting("home_banner_{$i}_button",$bannerDefaults[$i]['button']),
    'link'=>setting("home_banner_{$i}_link",$bannerDefaults[$i]['link'])
  ];
}
if(!$slides) $slides=[$bannerDefaults[1]];
foreach($slides as $idx=>$slide): ?>
<div class="hero-slide <?= $idx===0?'active':'' ?>">
  <img src="<?=h($slide['image'])?>" alt="Rishtewale banner">
  <div class="hero-slide-overlay"></div>
  <div class="hero-slide-copy">
    <span class="hero-slide-pill">💗 <?=h(setting('home_online_count','24+'))?>+ Profiles Online</span>
    <h1><?=h($slide['title'])?></h1>
    <p><?=h($slide['subtitle'])?></p>
    <a href="<?=h($slide['link'])?>"><?=h($slide['button'])?> →</a>
  </div>
</div>
<?php endforeach; ?>
<?php if(count($slides)>1): ?><div class="hero-dots"><?php foreach($slides as $idx=>$slide): ?><button type="button" class="hero-dot <?= $idx===0?'active':'' ?>" data-slide="<?=$idx?>" aria-label="Slide <?=$idx+1?>"></button><?php endforeach; ?></div><?php endif; ?>
</div>
<div class="hero-summary"><div><div class="pill">💗 <?=h(setting('home_online_count','24+'))?> Profiles</div><p><?=h(setting('home_subtitle','Verified profiles • Safe & simple registration'))?></p></div><div class="hero-online"><span class="online-dot"></span> Online</div></div>
<div class="trust-strip"><span>🟢 <?=h(setting('home_online_count','24+'))?> Online</span><span>✓ Verified</span><span>🔒 Private</span></div>
<div class="slider mini-profile-slider"><?php for($i=1;$i<=6;$i++): $n=setting('home_profile_'.$i.'_name',''); $c=setting('home_profile_'.$i.'_city',''); $im=setting('home_profile_'.$i.'_photo',''); if(!$n){$n=['Priya','Neha','Pooja','Riya','Anjali','Simran'][$i-1];} if(!$c){$c=['Lucknow','Kanpur','Delhi','Jaipur','Mahoba','Agra'][$i-1];} if(!$im){$im='https://randomuser.me/api/portraits/women/'.(($i*11+12)%90).'.jpg';} ?><div class="mini"><div class="avatar-wrap"><img class="avatar-img" src="<?=h($im)?>" alt="Profile"><span class="mini-online">●</span></div><b><?=h($n)?></b><small><?=h($c)?></small><em>● Online</em></div><?php endfor; ?></div><div class="hero-note"><?=h(setting('home_note','✨ Aaj naye profiles online hain — register karke matches dekhein'))?></div>
</section><section class="card"><h2>Register / पंजीकरण</h2><?php if(!empty($err)):?><div class="error"><?=h($err)?></div><?php endif;?><form method="post"><label>आप कौन हैं?</label><div class="gender"><label><input type="radio" name="gender" value="male" required> 👨 Male</label><label><input type="radio" name="gender" value="female"> 👩 Female</label></div><input name="age" type="number" min="18" max="100" placeholder="Your Age / उम्र" required><input name="name" placeholder="Name / नाम" required><select name="state" required><option value="">State / राज्य</option><?php foreach(['Uttar Pradesh','Delhi','Maharashtra','Bihar','Rajasthan','Madhya Pradesh','Haryana','Punjab','Gujarat','West Bengal','Uttarakhand','Other'] as $x):?><option><?=h($x)?></option><?php endforeach;?></select><input name="city" placeholder="City / शहर" required><input name="whatsapp" placeholder="WhatsApp Number" required><input name="telegram" placeholder="Telegram Username (optional)"><button>Continue →</button></form></section></main><script>
(function(){
 const root=document.querySelector('.hero-slider'); if(!root)return;
 const slides=[...root.querySelectorAll('.hero-slide')],dots=[...root.querySelectorAll('.hero-dot')]; if(slides.length<2)return;
 let idx=0,timer;
 function show(n){idx=(n+slides.length)%slides.length;slides.forEach((x,i)=>x.classList.toggle('active',i===idx));dots.forEach((x,i)=>x.classList.toggle('active',i===idx));}
 function start(){timer=setInterval(()=>show(idx+1),Number(root.dataset.interval)||4000)}
 dots.forEach(d=>d.addEventListener('click',()=>{show(Number(d.dataset.slide));clearInterval(timer);start();}));
 let sx=0;root.addEventListener('touchstart',e=>sx=e.touches[0].clientX,{passive:true});root.addEventListener('touchend',e=>{let dx=e.changedTouches[0].clientX-sx;if(Math.abs(dx)>45){show(idx+(dx<0?1:-1));clearInterval(timer);start();}},{passive:true});
 start();
})();
</script><script src="/rishtewale-protection.js"></script>
</body></html>
