If you like to embed an iframe in your website, but only as an overlay if a user clicks a bubble/balloon, then this is going to save you a lot of time.

Trying to integrate [embeddable AI chats](https://www.linkedin.com/posts/liorweissbrod_openai-chatgpt-aibusiness-activity-7287060823183675392-17Q7) raised the general question of how to **easily** integrate iframes as bubbles/ballons.

WordPress specifically has various related extensions, but none of them (not even Elementor) has direct support (at least for free) for bubbles/balloons turning to iframes.

# Goals
1. Allowing to embed any iframe in any website
1. Supporting simultaneous embedding
1. Avoiding loading iframes unless clicked upon
1. Using HTML shapes to avoid loading images
1. Animating the bubble/balloon opening the iframe

# Usage
1. Replace the text with your site's text.
1. Replace the iframe tags with your ones.
1. (Optionally) `dualchat` setting: control if you want multiple chats at the same time (default is no).
1. (Optionally) `chatlang` setting: choose which languages you want (default is all).

## WordPress
The WordPress version uses [add_action](https://developer.wordpress.org/reference/functions/add_action/) with [wp_enqueue_scripts](https://developer.wordpress.org/reference/functions/wp_enqueue_scripts/) and [wp_footer](https://developer.wordpress.org/reference/functions/wp_footer/) to add the CSS/JS and HTML respectively.

It can be applied easily with an extension like [Code Snippets](https://wordpress.org/plugins/code-snippets/).

# Screenshot
![balloons](https://github.com/user-attachments/assets/930138d5-54ff-4379-8ec4-e3d853bfb00a)
