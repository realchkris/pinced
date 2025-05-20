<?php

return [

	'blocked_domains' => [
		'tripadvisor.com',
		'facebook.com',
		'yelp.com',
		'ubereats.com',
		'opentable.com',
		'doordash.com',
		'grubhub.com',
		'seamless.com',
		'zomato.com',
	],

	'proxy_sources' => [
		'https://free-proxy-list.net/',
		'https://sslproxies.org/',
		'https://spys.one/',
	],

	'user_agents' => [
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.10 Safari/605.1.1',
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.3',
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.3',
		'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.3',
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Trailer/93.3.8652.5',
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.',
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36 Edg/131.0.0.',
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.',
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 Edg/132.0.0.',
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36 Edge/18.1958',
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:136.0) Gecko/20100101 Firefox/136.',
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.3',
	],

	'address_keywords' => [
        'street', 'st.', 'avenue', 'ave.', 'road', 'rd.', 
        'boulevard', 'blvd.', 'plaza', 'square', 'place',
        'rue', 'via', 'straße', 'str.', 'allee', 'weg', 'cours',
    ],

	'noise_keywords' => [
        'subscribe', 'newsletter', 'share', 'contact', 'author', 'blog',
        'comment', 'join', 'login', 'sign up', 'language', 'account',
        'cookie', 'privacy', 'terms', 'about', 'categories', 'tags', 'search',
		'photo', 'member', 'toggle', 'navigation'
    ],

];
