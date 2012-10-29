<?php
	/** @var OpenIDUserController $this */
//	$this->pageHeader = 'SignIn throughs OpenID';

	$this->layout = 'column1';

	$ru = $this->getContUrl();
	$providers = array(
		array(
			'name'=>'Vật Giá',
			'logo'=>$this->assetsUrl.'/images/logo-vatgia-square.png',
			'link'=>$this->createUrl('', array('p'=>'vg','ru'=>$ru)),
		),
		array(
			'name'=>'Google',
			'logo'=>$this->assetsUrl.'/images/google_logo.jpg',
			'link'=>$this->createUrl('', array('p'=>'google','ru'=>$ru)),
		),
		array(
			'name'=>'Yahoo',
			'logo'=>$this->assetsUrl.'/images/yahoo_logo.png',
			'link'=>$this->createUrl('', array('p'=>'yahoo','ru'=>$ru)),
		),
	);
?>

<div>
	<p class="lead" style="text-align: center;">Choose your OpenID Providers

    <?php foreach ($providers as $p): ?>
    <a href="<?php echo $p['link']; ?>">
    	<img src="<?php echo $p['logo']; ?>" alt="<?php echo $p['name']; ?>">
    </a>
    <?php endforeach; ?>

    </p>
</div>