<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
</head>

<body class="bg-white">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Pet Rental Page</title>
</head>
<body class="w-full">
    <!-- Hero Image -->
    <div>
        <img src="<?php echo esc_url(site_url('/component/image//Frame 107.png')); ?>" alt="imagepicture" class="sm:block hidden w-full" style="height: 500px;" />
        <img src="<?php echo esc_url(site_url('/component/image//Frame 107.png')); ?>" alt="imagepicture" class="w-full block sm:hidden" style="height: 240px;" />
    </div>

    <p data-aos="fade-up" class="text-center sm:w-4/12 w-72 mx-auto text-[#d9286c] font-bold text-sm my-6">
        Discover the Joy of Pet Ownership Without Long-Term Commitments
    </p>

    <!-- Additional Blog Cards -->
     <div data-aos="zoom-in">
    <?php
$blogs = [
    ["img" => "component/image/Frame 108.png", "bgColor" => "#20daaf", "textColor" => "#e5e7eb", "title" => "The Joy of Renting a Pet: Why It’s Perfect for Everyone", "desc" => "Explore how renting a pet can bring happiness to families, individuals, and even workplaces.."],
    ["img" => "component/image/Frame 110.png", "bgColor" => "#ffd2d2", "textColor" => "#d9286c", "title" => "How to Choose the Perfect Pet for Your Needs", "desc" => "A guide on selecting the right pet based on lifestyle, space availability, and personality."],
    ["img" => "component/image/Frame 112.png", "bgColor" => "#20daaf", "textColor" => "#e5e7eb", "title" => "Benefits of Renting Pet Accessories and Supplies", "desc" => "Discuss the cost-effectiveness and convenience of renting pet essentials."]
];

foreach ($blogs as $blog) {
    echo "<div class='text-white flex flex-row gap-5 my-6 flex-wrap sm:flex-nowrap justify-center mx-auto px-2 items-center' >
        <img src='" . esc_url(site_url($blog['img'])) . "' alt='imagepicture' class='sm:h-72 sm:w-6/12 h-52 lg:relative lg:left-2'/>
        <div class='mx-auto flex flex-col items-center py-9 sm:py-12 sm:h-72 sm:w-6/12 rounded-xl' style='background-color: {$blog['bgColor']}'>
            <h1 class='font-bold w-80 text-center' style='color: {$blog['textColor']}'>{$blog['title']}</h1>
            <p class='w-72 font-bold text-center my-6' style='font-size: 14px; width: 360px; color: {$blog['textColor']}'>{$blog['desc']}</p>
            <div class='flex flex-row justify-between gap-16 sm:gap-24 items-center'>
                <h1 class='font-bold text-sm text-black'>24 January 2025</h1>
                <button class='p-1 w-20 text-sm rounded' style='background-color: {$blog['textColor']}; color: black font-bold;'>More</button>
            </div>
        </div>
    </div>";
}
?>

</div>

    <!-- Final Section with Image Grid -->
    <div data-aos="fade-up" class="text-white flex mb-4 mt-6 mb-6 sm:translate-y-6 flex-row sm:gap-5 gap-2 flex-wrap justify-center mx-auto px-2 items-center">
    <img src="<?php echo esc_url(site_url('/component/image/Frame 116.png')); ?>" alt="imagepicture" class="sm:h-64 h-52" />
    <img src="<?php echo esc_url(site_url('/component/image/Frame 117.png')); ?>" alt="imagepicture" class="sm:h-64 h-52" />
    </div>

    <h1 data-aos="fade-right" class="font-bold sm:w-96 my-6 w-72 translate-y-1  text-center text-black mx-auto">
        Bringing Pets and People Together – One Rental at a Time
    </h1>

    <!-- Three Quote Cards -->
    <div class="text-white flex mt-4 flex-row gap-4 translate-y-14 mb-20 flex-wrap justify-center mx-auto px-2 items-center mb-6">
    <?php
        $quotes = [
            ["img" => "component/image/Frame 124.png", "text" => "Experience the Love of Pets Without the Commitment"],
            ["img" => "component/image/Frame 125.png", "text" => "Creating Moments of Joy with Pets and Essentials"],
            ["img" => "component/image/Frame 126.png", "text" => "Making Pet Ownership Easy, Affordable, and Flexible"]
        ];
        
        // Define different AOS animations
        $aos_animations = ["fade-up", "zoom-in", "flip-left"];

        foreach ($quotes as $index => $quote) {
            $aos_animation = $aos_animations[$index % count($aos_animations)]; // Rotate animations
            echo "<div data-aos='$aos_animation' class='flex flex-col border rounded w-64 sm:w-96 border-2 shadow-md border-green-100 items-center justify-center text-center'>
                <img src='" . esc_url(site_url($quote['img'])) . "' alt='picture' class='w-full' />
                <h1 class='w-48 my-2 text-black' style='font-size: 11px;'>{$quote['text']}</h1>
                <p class='text-[#d9286c] text-sm mb-3'>Read More</p>
            </div>";
        }
    ?>
</div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    AOS.init();
</script>
</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    AOS.init({
        duration: 1000, // Animation duration
        easing: 'ease-in-out',
        once: true // Only animate once
    });
</script>
</html>