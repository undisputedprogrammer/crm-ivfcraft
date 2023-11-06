<?php
namespace App\View\Composers;

use Illuminate\View\View;

class LogoComposer
{
    public function compose(View $view): void
    {
        $user = auth()->user();
        if ($user != null) {
            $h = $user->hospital;
            if($h->code == 'craft') {
                $logo = "/images/craft_logo.png";
            } elseif ($h->code == 'ar') {
                $logo = "/images/ar_logo.png";
            }
        } else {
            $logo = 'images/combined-logo.png';
        }
        $view->with('appLogo', $logo);
    }
}
?>
