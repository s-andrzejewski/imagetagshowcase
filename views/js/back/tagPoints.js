export const tagPoints = (image, event) => {
    const point = tagNewPoint(event);
    image.parentNode.appendChild(point);

    const pointsInput = document.querySelector('[name="IMAGETAGSHOWCASE_POINTS"]');
    generateInputForProductID(point);
    setTimeout(()=>{
        // prevents an issue with reading popper children with querySelector
        inputPointCoordsToForm(pointsInput, point);
    }, 100);
}

function tagNewPoint(e) {  
    const rect = e.target.getBoundingClientRect();
    const left = e.clientX - rect.left;
    const top = e.clientY - rect.top;
    console.log("Left: " + left + "; Top: " + top + ".");
    
    const point = document.createElement('button');
    point.classList.add('tag-point');
    point.style.cssText = `
        top: ${top}px;
        left: ${left}px;
    `;
    point.setAttribute("title", "Kliknij aby przypisać produkt");
    point.setAttribute("type", "button");

    return point;
}

function generateInputForProductID(pointElement) {
    const scriptSrc = 'https://unpkg.com/@popperjs/core@2';

    // Check if a script with the same src is already in the head
    const existingScript = document.querySelector(`script[src="${scriptSrc}"]`);

    if (existingScript) {
        initializePopper(pointElement);
    } else {
        const script = document.createElement('script');
        script.src = scriptSrc;
        script.onload = () => {
            // Popper.js has been loaded
            initializePopper(pointElement);
        };
        document.head.appendChild(script);
    }
}


function initializePopper(pointElement){
    const tooltip = document.createElement('div');
    tooltip.classList.add("tag-tooltip");

    const text = document.createElement('span');
    text.innerText = 'Podaj id produktu:';
    tooltip.appendChild(text);
    
    const inputProductId = document.createElement('input');
    inputProductId.classList.add('input-product-id');
    tooltip.appendChild(inputProductId);

    const saveProductId = document.createElement('button');
    saveProductId.classList.add('save-product-id');
    saveProductId.setAttribute("type", "button");
    saveProductId.setAttribute("title", "Zapisz id produktu");
    saveProductId.textContent = "Zapisz";
    tooltip.appendChild(saveProductId);
    
    pointElement.appendChild(tooltip);

    const popperInstance = Popper.createPopper(pointElement, tooltip, {
        placement: 'right',
    });
}

function inputPointCoordsToForm(inputElement, pointElement) {
    console.log("input hidden: ", inputElement);
    
    const inputProductId = pointElement.querySelector('.input-product-id')
    const saveProductId = pointElement.querySelector('.save-product-id')
    console.log(saveProductId);
    
    const handler = () => {      
        const pointObj = {
            prodId: inputProductId.value,
            top: pointElement.style.top,
            left: pointElement.style.left,
        }
        
        const pointJSON = JSON.stringify(pointObj);
        
        if(!inputElement.value) {
            inputElement.value = `${pointJSON}`;
        }
        else {
            inputElement.value += `, ${pointJSON}`;
        }
        
        saveProductId.removeEventListener("click", handler)
    }
    saveProductId.addEventListener("click", handler)
}
