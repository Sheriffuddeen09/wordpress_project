<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo('name'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?php wp_head(); ?>
    

</head>
<body <?php body_class(); ?>>

<header class="bg-white mb-3 shadow-md -mb-0">
    <div class="head font-roboto text-center font-bold text-xs bg-green-500 text-white p-1 -mb-2">
        Special Discount Alert: Flat 20% Off on Your Rental Needs!
    </div>
    
    <div class="flex flex-row justify-between items-center text-black p-2 shadow-md">
        <div class="inline-flex sm:gap-6 items-center">
            <span>
                <a href="<?php echo esc_url(home_url('/')); ?>">
                <img src="<?php echo esc_url(site_url('/layout/image/logo.png')); ?>" alt="Logo" class='w-20 h-14'>

                </a>
            </span>
            
            <nav class="sm:block hidden">
                <ul class="inline-flex gap-6 mt-4 text-sm">
                <li>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="hover:bg-gray-100 px-2 py-1 rounded <?php if (is_front_page()) echo 'border-b-2 border-pink-500'; ?>">Home</a>
                </li>
                <li>
                    <a href="<?php echo esc_url(home_url('/shop')); ?>" class="hover:bg-gray-100 px-2 py-1 rounded <?php if (is_page('shop')) echo 'border-b-2 border-pink-500'; ?>">Shop</a>
                </li>
                    <li>
                        <a href="<?php echo esc_url(home_url('/category')); ?>" class="hover:bg-gray-100 px-2 py-1 <?php if (is_page('category')) echo 'border-b-2 border-pink-500'; ?> rounded">Category</a>
                        <ul class="dropdown-menu hidden">
                            <?php
                            $categories = get_categories(); 
                            foreach ($categories as $category) {
                                echo '<li><a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a></li>';
                            }
                            ?>
                        </ul>
                    </li>
                    <li><a href="<?php echo esc_url(home_url('/about')); ?>" class="hover:bg-gray-100 px-2 py-1 rounded <?php if (is_page('about')) echo 'border-b-2 border-pink-500'; ?>">About</a></li>
                    <li><a href="<?php echo esc_url(home_url('/blogs')); ?>" class="hover:bg-gray-100 px-2 py-1 rounded <?php if (is_page('blogs')) echo 'border-b-2 border-pink-500'; ?>">Blogs</a></li>
                </ul>
            </nav>
        </div>
        
        <div class="inline-flex sm:gap-0 mt-4 text-sm mr-5 items-center">
            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="hover:bg-gray-100 px-2 py-1 rounded sm:block hidden <?php if (is_page('contact')) echo 'border-b-2 border-pink-500'; ?>">Help Center</a>

            
      <!-- Country Select Dropdown -->
<div id="countrySelectContainer" class="relative flex items-center sm:mr-4 gap-8">
    <img id="selectedFlag" src="https://flagcdn.com/w40/us.png" class="w-6 h-6 rounded-full  relative left-14" alt="Flag">
    <select id="countrySelect" class="px-2 py-1 rounded border-pink-700 border-2 border w-24 py-2 px-5 ">
        <option value="+1" data-flag="https://flagcdn.com/w40/us.png"> United States (+1)</option>
        <option value="+44" data-flag="https://flagcdn.com/w40/gb.png">United Kingdom (+44)</option>
        <option value="+1" data-flag="https://flagcdn.com/w40/ca.png"> Canada (+1)</option>
        <option value="+61" data-flag="https://flagcdn.com/w40/au.png">Australia (+61)</option>
        <option value="+49" data-flag="https://flagcdn.com/w40/de.png">Germany (+49)</option>
        <option value="+33" data-flag="https://flagcdn.com/w40/fr.png">France (+33)</option>
        <option value="+91" data-flag="https://flagcdn.com/w40/in.png">India (+91)</option>
        <option value="+234" data-flag="https://flagcdn.com/w40/ng.png">Nigeria (+234)</option>
    </select>
    
    
    <div class="relative">
    <button id="searchToggle" class="p-2 rounded-full hover:bg-gray-300">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-black">
  <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
</svg>
<!-- Search Icon -->
    </button>

    <div id="searchBox" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <form action="<?php echo esc_url(home_url('/')); ?>" method="get" class="bg-white p-4 rounded-lg shadow-md w-1/2">
            <div class="flex">
                <input type="text" name="s" placeholder="Search products..." class="w-full outline-none px-4 py-2 border rounded-l-md">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-r-md">
                    <i class="fas fa-search"></i> <!-- Search Button Icon -->
                </button>
            </div>
        </form>
    </div>
</div>

<!-- User Dashboard Icon -->
<a href="<?php echo esc_url(home_url('/dashboard')); ?>" class="hover:bg-gray-200  <?php if (is_page('dashboard')) echo 'border-b-2 border-pink-500'; ?> p-2 rounded-full">
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-black">
  <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
</svg>

</a>

</div>
<!-- Cart Icon -->
<?php  if (function_exists('wc_get_cart_url')): ?>
    <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="relative inline-flex items-center hover:bg-gray-100 px-2 py-1 rounded-full">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-black">
  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
</svg>

        <span class="absolute -right-2 -top-2 bg-red-500 text-white rounded-full text-xs px-2 py-1">
            <?php echo WC()->cart->get_cart_contents_count(); ?>
        </span>
    </a>
<?php endif; ?>

</div>
    </div>
</header>

<script>
   
document.getElementById('searchToggle').addEventListener('click', function() {
    document.getElementById('searchBox').classList.toggle('hidden');
});

document.addEventListener('DOMContentLoaded', function() {
    const countrySelect = document.getElementById('countrySelect');
    const selectedFlag = document.getElementById('selectedFlag');

    function updateCountryDisplay() {
        const selectedOption = countrySelect.options[countrySelect.selectedIndex];
        const flagUrl = selectedOption.getAttribute('data-flag');
        selectedFlag.src = flagUrl;
    }

    countrySelect.addEventListener('change', updateCountryDisplay);
    updateCountryDisplay(); // Set initial flag on page load
});

</script>

<?php wp_footer(); ?>
</body>
</html>
