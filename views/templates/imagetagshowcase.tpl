{* 
    $heading = Configuration::get('IMAGETAGSHOWCASE_HEADER');
    $desc = Configuration::get('IMAGETAGSHOWCASE_DESCRIPTION');
    $image = Configuration::get('IMAGETAGSHOWCASE_IMAGE');
    $points = Configuration::get('IMAGETAGSHOWCASE_POINTS');
*}

<section class="imagetagshowcase__container">
    <div class="imagetagshowcase__text-container">
        <h2>{$heading}</h2>
        <p>{$desc}</p>
    </div>
    <div class="imagetagshowcase__image-container">
        <img src="{$image}" alt="Image" data-points="{$points}">
        <button type="button" class="tag-point"></button>
    </div>
</section>

<script>
    const imageContainer = document.querySelector('.imagetagshowcase__image-container')
    console.log(imageContainer);

    const image = document.querySelector('[data-points]');
    console.log(image);

    const pointsObject = JSON.parse(image.dataset.points);
    console.log(pointsObject);

    const pointButton = document.querySelector('.tag-point');
    console.log(pointButton);

    pointsObject.forEach((point)=>{
        console.log(point);
        
        const newPointElement = pointButton.cloneNode(true);
        imageContainer.appendChild(newPointElement);

        newPointElement.style.top = point.top;
        newPointElement.style.left = point.left;
        newPointElement.style.display = "flex";
    });
</script>
