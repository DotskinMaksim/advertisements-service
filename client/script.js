// URL вашего API
const apiUrl = "http://localhost:8000/api/ads";

// Функция для получения и отображения объявлений
// Асинхронная функция для получения и отображения объявлений
async function fetchAds() {
    try {
        const response = await fetch(apiUrl);

        if (!response.ok) {
            console.error(`Ошибка: ${response.status} ${response.statusText}`);
            return;
        }

        const ads = await response.json();

        const adsContainer = document.getElementById("ads-container");
        adsContainer.innerHTML = "";

        ads.forEach(ad => {
            const adItem = document.createElement("div");
            adItem.classList.add("ad-item");

            let imagesHtml = "";
            if (typeof ad.main_image === "string") {
                try {
                    const images = JSON.parse(ad.main_image);
                    imagesHtml = images.map(img => `<img src="${img}" alt="Image of ${ad.title}">`).join("");
                } catch (error) {
                    imagesHtml = `<img src="${ad.main_image}" alt="Image of ${ad.title}">`;
                }
            }

            adItem.innerHTML = `
                <h3>${ad.title}</h3>
                <p>Цена: ${ad.price} ₽</p>
                <div>${imagesHtml}</div>
                <p>Создано: ${ad.created_at}</p>
            `;
            adsContainer.appendChild(adItem);
        });
    } catch (error) {
        console.error("Ошибка при получении объявлений:", error);
    }
}
// Функция для добавления нового объявления
async function addAd(event) {
    event.preventDefault();

    const title = document.getElementById("title").value;
    const description = document.getElementById("description").value;

    try {
        const response = await fetch(apiUrl, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ title, description })
        });

        if (response.ok) {
            fetchAds();  // Обновляем список объявлений
            document.getElementById("add-ad-form").reset();
        } else {
            console.error("Ошибка при добавлении объявления:", await response.text());
        }
    } catch (error) {
        console.error("Ошибка при добавлении объявления:", error);
    }
}
document.addEventListener("DOMContentLoaded", fetchAds);