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
<footer class="bg-cover bg-center bg-no-repeat py-8 px-6" style="background-image: url('<?php echo esc_url(site_url('/layout/image/background.png')); ?>');">

    <div class="container mx-auto flex flex-wrap justify-around items-start gap-8">
        <!-- Left Section -->
        <div class="max-w-sm -mt-5">
            <!-- <?php if ( has_custom_logo() ) : ?> -->
                <div class="logo">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <?php the_custom_logo(); ?>
                    </a>
                </div>
            <!-- <?php else : ?> -->
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <img src="<?php echo esc_url(site_url('/layout/image/logo.png')); ?>" alt="Logo" class='w-28 h-16'>
                </a>
            <?php endif; ?>
            
            <p class="mt-2 text-sm text-black font-bold">
                Create heartwarming memories with this lovable Golden Retriever puppy – 
                the perfect addition to your special moments! 🐾
            </p>
            <div class="mt-4">
                <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST">
                    <input type="email" name="email" placeholder="Email Address" class="p-2 w-full border border-pink-400 rounded-xl focus:outline-none text-sm" />
                    <button type="submit" name="subscribe" class="mt-2 w-full text-sm bg-pink-600 text-white py-2 rounded-xl hover:bg-black transition">
                        Sign UP
                    </button>
                </form>
            </div>

            <!-- Social Icons -->
            <div class="flex space-x-4 mt-4">
                <a href="#" class="text-black text-xl"><i class="fab fa-facebook"></i></a>
                <a href="#" class="text-black text-xl"><i class="fab fa-instagram"></i></a>
                <a href="#" class="text-black text-xl"><i class="fab fa-google"></i></a>
                <a href="#" class="text-black text-xl"><i class="fab fa-x-twitter"></i></a>
                <a href="#" class="text-black text-xl"><i class="fab fa-linkedin"></i></a>
            </div>
        </div>

        <!-- Middle Section -->
        <div class="grid grid-cols-2 gap-6 text-sm">
            <div>
                <h3 class="font-bold text-black">Quick Links</h3>
                <ul class="space-y-1 mt-2">
                    <li><a href="<?php echo esc_url( home_url( '/about' ) ); ?>" class="hover:text-pink-700">About Us</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="hover:text-pink-700">Contact Us</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/blogs' ) ); ?>" class="hover:text-pink-700">Blog</a></li>
                    <li><a href="#" class="hover:text-pink-700">FAQ</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/help' ) ); ?>" class="hover:text-pink-700">Help</a></li>
                    <li><a href="#" class="hover:text-pink-700">Returns</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-bold text-black">Product</h3>
                <ul class="space-y-1 mt-2">
                    <li><a href="#" class="hover:text-pink-700">Pet Clothing</a></li>
                    <li><a href="#" class="hover:text-pink-700">Pet Carriers</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/category/dog-cat-beds' ) ); ?>" class="hover:text-pink-700">Dog & Cat Beds</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/shop' ) ); ?>" class="hover:text-pink-700">Shop</a></li>
                    <li><a href="#" class="hover:text-pink-700">Shipping</a></li>
                    <li><a href="#" class="hover:text-pink-700">Delivery</a></li>
                </ul>
            </div>
        </div>

        <!-- Right Section -->
        <div class="text-sm">
            <h3 class="font-bold text-black">Our Locations</h3>
            <p class="mt-2 inline-flex gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
                635 Rogers Street <br/> Downers Grove, IL 60515 USA
            </p>
            <p class="mt-1">
                <a href="tel:+1 (234) 547 234 4376" class="hover:text-pink-700">📞 +1 (234) 547 234 4376</a>
            </p>
            <div class="mt-4 flex sm:flex-col flex-row gap-2">
            <img src="<?php echo esc_url(site_url('/image/store.png')); ?>" alt="Logo" class='w-32 h-10'>
            <img src="<?php echo esc_url(site_url('/image/play.png')); ?>" alt="Logo" class='w-32 h-10'>
            </div>
        </div>
    </div>

    <hr class="my-6 border-pink-400"/>
    <p class="text-center text-sm font-bold">&copy; <?php echo date("Y"); ?> Shofx. All rights reserved.</p>
</footer>
<?php wp_footer(); ?>
</body>
</html>
