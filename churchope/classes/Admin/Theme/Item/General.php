<?php
/**
 * 'General' admin menu page
 */
class Admin_Theme_Item_General extends Admin_Theme_Menu_Item
{
	
	const FONT_LIST = 'Abel,Abril Fatface,Aclonica,Acme,Actor,Adamina,Advent Pro,Aguafina Script,Aladin,Aldrich,Alegreya,Alegreya SC,Alex Brush,Alfa Slab One,Alice,Alike,Alike Angular,Allan,Allerta,Allerta Stencil,Allura,Almendra,Almendra SC,Amarante,Amaranth,Amatic SC,Amethysta,Andada,Andika,Annie Use Your Telescope,Anonymous Pro,Antic,Antic Didone,Antic Slab,Anton,Arapey,Arbutus,Architects Daughter,Arimo,Arizonia,Armata,Artifika,Arvo,Asap,Asset,Astloch,Asul,Atomic Age,Aubrey,Audiowide,Average,Averia Gruesa Libre,Averia Libre,Averia Sans Libre,Averia Serif Libre,Bad Script,Balthazar,Bangers,Basic,Baumans,Belgrano,Belleza,Bentham,Berkshire Swash,Bevan,Bigshot One,Bilbo,Bilbo Swash Caps,Bitter,Black Ops One,Bonbon,Boogaloo,Bowlby One,Bowlby One SC,Brawler,Bree Serif,Bubblegum Sans,Buda,Buenard,Butcherman,Butterfly Kids,Cabin,Cabin Condensed,Cabin Sketch,Caesar Dressing,Cagliostro,Calligraffitti,Cambo,Candal,Cantarell,Cantata One,Capriola,Cardo,Carme,Carter One,Caudex,Cedarville Cursive,Ceviche One,Changa One,Chango,Chau Philomene One,Chelsea Market,Cherry Cream Soda,Chewy,Chicle,Chivo,Coda,Coda Caption,Codystar,Comfortaa,Coming Soon,Concert One,Condiment,Contrail One,Convergence,Cookie,Copse,Corben,Courgette,Cousine,Coustard,Covered By Your Grace,Crafty Girls,Creepster,Crete Round,Crimson Text,Crushed,Cuprum,Cutive,Damion,Dancing Script,Dawning of a New Day,Days One,Delius,Delius Swash Caps,Delius Unicase,Della Respira,Devonshire,Didact Gothic,Diplomata,Diplomata SC,Doppio One,Dorsa,Dosis,Dr Sugiyama,Droid Sans,Droid Sans Mono,Droid Serif,Duru Sans,Dynalight,EB Garamond,Eagle Lake,Eater,Economica,Electrolize,Emblema One,Emilys Candy,Engagement,Enriqueta,Erica One,Esteban,Euphoria Script,Ewert,Exo,Expletus Sans,Fanwood Text,Fascinate,Fascinate Inline,Federant,Federo,Felipa,Fjord One,Flamenco,Flavors,Fondamento,Fontdiner Swanky,Forum,Francois One,Fredericka the Great,Fredoka One,Fresca,Frijole,Fugaz One,Galdeano,Gentium Basic,Gentium Book Basic,Geo,Geostar,Geostar Fill,Germania One,Give You Glory,Glass Antiqua,Glegoo,Gloria Hallelujah,Goblin One,Gochi Hand,Gorditas,Goudy Bookletter 1911,Graduate,Gravitas One,Great Vibes,Gruppo,Gudea,Habibi,Hammersmith One,Handlee,Happy Monkey,Henny Penny,Herr Von Muellerhoff,Holtwood One SC,Homemade Apple,Homenaje,IM Fell DW Pica,IM Fell DW Pica SC,IM Fell Double Pica,IM Fell Double Pica SC,IM Fell English,IM Fell English SC,IM Fell French Canon,IM Fell French Canon SC,IM Fell Great Primer,IM Fell Great Primer SC,Iceberg,Iceland,Imprima,Inconsolata,Inder,Indie Flower,Inika,Irish Grover,Istok Web,Italiana,Italianno,Jim Nightshade,Jockey One,Jolly Lodger,Josefin Sans,Josefin Slab,Judson,Julee,Junge,Jura,Just Another Hand,Just Me Again Down Here,Kameron,Karla,Kaushan Script,Kelly Slab,Kenia,Knewave,Kotta One,Kranky,Kreon,Kristi,Krona One,La Belle Aurore,Lancelot,Lato,League Script,Leckerli One,Ledger,Lekton,Lemon,Lilita One,Limelight,Linden Hill,Lobster,Lobster Two,Londrina Outline,Londrina Shadow,Londrina Sketch,Londrina Solid,Lora,Love Ya Like A Sister,Loved by the King,Lovers Quarrel,Luckiest Guy,Lusitana,Lustria,Macondo,Macondo Swash Caps,Magra,Maiden Orange,Mako,Marck Script,Marko One,Marmelad,Marvel,Mate,Mate SC,Maven Pro,Meddon,MedievalSharp,Medula One,Megrim,Merienda One,Merriweather,Metamorphous,Metrophobic,Michroma,Miltonian,Miltonian Tattoo,Miniver,Miss Fajardose,Modern Antiqua,Molengo,Monofett,Monoton,Monsieur La Doulaise,Montaga,Montez,Montserrat,Mountains of Christmas,Mr Bedfort,Mr Dafoe,Mr De Haviland,Mrs Saint Delafield,Mrs Sheppards,Muli,Mystery Quest,Neucha,Neuton,News Cycle,Niconne,Nixie One,Nobile,Norican,Nosifer,Nothing You Could Do,Noticia Text,Nova Cut,Nova Flat,Nova Mono,Nova Oval,Nova Round,Nova Script,Nova Slim,Nova Square,Numans,Nunito,Old Standard TT,Oldenburg,Oleo Script,Open Sans,Open Sans Condensed,Orbitron,Original Surfer,Oswald,Over the Rainbow,Overlock,Overlock SC,Ovo,Oxygen,PT Mono,PT Sans,PT Sans Caption,PT Sans Narrow,PT Serif,PT Serif Caption,Pacifico,Parisienne,Passero One,Passion One,Patrick Hand,Patua One,Paytone One,Permanent Marker,Petrona,Philosopher,Piedra,Pinyon Script,Plaster,Play,Playball,Playfair Display,Podkova,Poiret One,Poller One,Poly,Pompiere,Pontano Sans,Port Lligat Sans,Port Lligat Slab,Prata,Press Start 2P,Princess Sofia,Prociono,Prosto One,Puritan,Quando,Quantico,Quattrocento,Quattrocento Sans,Questrial,Quicksand,Qwigley,Radley,Raleway,Rammetto One,Rancho,Rationale,Redressed,Reenie Beanie,Revalia,Ribeye,Ribeye Marrow,Righteous,Rochester,Rock Salt,Rokkitt,Ropa Sans,Rosario,Rosarivo,Rouge Script,Ruda,Ruge Boogie,Ruluko,Ruslan Display,Russo One,Ruthie,Sail,Salsa,Sancreek,Sansita One,Sarina,Satisfy,Schoolbell,Seaweed Script,Sevillana,Shadows Into Light,Shadows Into Light Two,Shanti,Share,Shojumaru,Short Stack,Sigmar One,Signika,Signika Negative,Simonetta,Sirin Stencil,Six Caps,Slackey,Smokum,Smythe,Sniglet,Snippet,Sofia,Sonsie One,Sorts Mill Goudy,Special Elite,Spicy Rice,Spinnaker,Spirax,Squada One,Stardos Stencil,Stint Ultra Condensed,Stint Ultra Expanded,Stoke,Sue Ellen Francisco,Sunshiney,Supermercado One,Swanky and Moo Moo,Syncopate,Tangerine,Telex,Tenor Sans,The Girl Next Door,Tienne,Tinos,Titan One,Trade Winds,Trocchi,Trochut,Trykker,Tulpen One,Ubuntu,Ubuntu Condensed,Ubuntu Mono,Ultra,Uncial Antiqua,UnifrakturCook,UnifrakturMaguntia,Unkempt,Unlock,Unna,VT323,Varela,Varela Round,Vast Shadow,Vibur,Vidaloka,Viga,Voces,Volkhov,Vollkorn,Voltaire,Waiting for the Sunrise,Wallpoet,Walter Turncoat,Wellfleet,Wire One,Yanone Kaffeesatz,Yellowtail,Yeseva One,Yesteryear,Zeyada';
	
	public function __construct($parent_slug = '')
	{
		
		$this->setPageTitle('General');
		$this->setMenuTitle('General');
		$this->setCapability('administrator');
		$this->setMenuSlug(SHORTNAME.'_general');
		parent::__construct($parent_slug);
		$this->init();
	}

	public function init()
	{
		
		$option = new Admin_Theme_Element_Pagetitle();
		$option->setName('General Settings');
		$this->addOption($option);
		$option = null;		
		
		$option = new Admin_Theme_Element_Select();
		$option->setName('Choose a Font')
				->setDescription('Choose a Font for titles, etc.')
				->setId(SHORTNAME."_gfont")
				->setStd('Open Sans')
				->setCustomized()					// Show this element on WP Customize Admin menu
				->setOptions($this->getFonts());
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Checkbox();
		$option->setDescription ('Disable Google fonts')
				->setName ('Check to disable Google fonts')
				->setCustomized()					// Show this element on WP Customize Admin menu
				->setId (SHORTNAME."_gfontdisable");
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Separator();
		$this->addOption($option);
		$option = null;
		
			
		$option = new Admin_Theme_Element_Text();
		$option->setName('Favicon URL')
				->setDescription('Type full URL like a <em>http://url.com/favicon.ico</em>')
				->setId(SHORTNAME."_favicon")
				->setStd(get_stylesheet_directory_uri() .'/images/favicon.ico');
		$this->addOption($option);
		$option = null;
		
		$option = new Admin_Theme_Element_Checkbox();
			$option->setName('Disable login page styling')
					->setDescription('Check to disable login page styling')
					->setId(SHORTNAME."_loginstyledisable")
					->setStd('');
			$this->addOption($option);
			$option = null;
			
		$option = new Admin_Theme_Element_Checkbox();
			$option->setName('Disable responsive')
					->setDescription('Check to disable responsive support')
					->setId(SHORTNAME."_responsive")
					->setStd('');
			$this->addOption($option);
			$option = null;
		
		$option = new Admin_Theme_Element_Separator();
		$this->addOption($option);
		$option = null;
		
			$option = new Admin_Theme_Element_Colorchooser();
			$option->setName('Content text color')
					->setDescription('Please select your custom color for all elements on light background.')
					->setId( SHORTNAME."_textcolor")
					->setStd("#797979");
			$this->addOption($option);

			$option = new Admin_Theme_Element_Colorchooser();
			$option->setName('Content headings color')
					->setDescription('Please select your custom color for all elements on dark background (footer area).')
					->setId(SHORTNAME."_headingscolor")
					->setStd('#545454');
			$this->addOption($option);

			$option = new Admin_Theme_Element_Colorchooser();
			$option->setName('Content links color')
					->setDescription('Please select your custom color for all elements on dark background (footer area).')
					->setId(SHORTNAME."_linkscolor")
					->setStd('#c62b02');
			$this->addOption($option);
			
		if (isset($_GET['preview']))
		{
			$option = new Admin_Theme_Element_Checkbox();
			$option->setName('Show preview switcher')
					->setDescription('Check to show preview color switcher')
					->setId(SHORTNAME."_preview")
					->setStd('');
			$this->addOption($option);
			$option = null;
		}	
	}
	
	/**
	 * Fonts list
	 * @return array
	 */
	private function getFonts()
	{
		return explode(',', self::FONT_LIST);
	}
}
?>
