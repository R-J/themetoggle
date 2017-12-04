<?php
$PluginInfo['themetoggle'] = [
    'Name' => 'Theme Toggle',
    'Description' => 'Add button which allows users to toggle between desktop and mobile theme',
    'Version' => '0.1.0',
    'RequiredApplications' => ['Vanilla' => '>= 2.3.1'],
    'SettingsPermission' => 'Garden.Settings.Manage',
    'MobileFriendly' => true,
    'HasLocale' => true, // only "ThemeToggleButton"
    'Author' => 'Robin Jurinka',
    'AuthorUrl' => 'https://open.vanillaforums.com/profile/r_j',
    'License' => 'MIT'
];

class ThemeTogglePlugin extends Gdn_Plugin {
    /**
     * Add toggle button to each page.
     *
     * @param GardenController $sender Instance of the calling class.
     *
     * @return void.
     */
    public function base_afterBody_handler($sender) {
        if (isMobile()) {
            $targetTheme = 'Mobile';
        } else {
            $targetTheme = 'Desktop';
        }
        // Text of button is "ThemeToggleButton", translated to a SVG picture.
        $link = anchor(
            t(
                'ThemeToggleButton',
                '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd"><path d="M24 8.2c0-.318-.126-.623-.351-.849-.226-.225-.531-.351-.849-.351h-6.6c-.318 0-.623.126-.849.351-.225.226-.351.531-.351.849v13.6c0 .318.126.623.351.849.226.225.531.351.849.351h6.6c.318 0 .623-.126.849-.351.225-.226.351-.531.351-.849v-13.6zm-11 14.8h-8l2.599-3h5.401v3zm6.5-1c-.553 0-1-.448-1-1s.447-1 1-1c.552 0 .999.448.999 1s-.447 1-.999 1zm3.5-3v-9.024h-7v9.024h7zm-2-14h-2v-2h-17v13h11v2h-13v-17h21v4zm-.5 4c.276 0 .5-.224.5-.5s-.224-.5-.5-.5h-2c-.276 0-.5.224-.5.5s.224.5.5.5h2z"/></svg>'
            ),
            'plugin/themetoggle/'.$targetTheme,
            'Button Hijack'
        );
        echo '<style>.ThemeToggle{position:fixed;right:0;bottom:2em;}</style>',
            '<div class="ThemeToggle">'.$link.'</div>';
    }

    /**
     * Plugins endpoint: toggles value of cookie and reloads page.
     *
     * @param PluginController $sender Instance of the calling class.
     *
     * @return void.
     */
    public function pluginController_themeToggle_create($sender) {
        Gdn::session()->setCookie(
            'IsMobileTheme',
            !Gdn::session()->getCookie('IsMobileTheme', false),
            1209600 // 14 days
        );
        $sender->jsonTarget('', '', 'Refresh');
        $sender->render('blank', 'utility', 'dashboard');
    }

    /**
     * Set mobile/desktop theme before every page render.
     *
     * @param GardenController $sender Instance of the calling class.
     *
     * @return void.
     */
    public function base_render_before($sender) {
        isMobile((bool)Gdn::session()->getCookie('IsMobileTheme', false));
    }
}
