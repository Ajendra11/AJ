const apiKey = "a9aae5d26f5f1a3c2c462695a237efba";
const apiUrl = "https://api.openweathermap.org/data/2.5/weather";
const defaultCity = "Pokhara";

// Selecting elements for displaying weather
const searchInput = document.querySelector("#search");
const button = document.querySelector("#searchbtn");
const cityText = document.querySelector("#cityText");
const date = document.querySelector("#date");
const weather_con = document.querySelector("#weather_condition");
const weather_description = document.querySelector("#description");
const weather_icon = document.querySelector("#weather_icon");
const Temperature = document.querySelector("#temperature span");
const Pressure = document.querySelector("#pressure");
const Humidity = document.querySelector("#humidity");
const Wind = document.querySelector("#WindSpeed");
const direction = document.querySelector("#windDirection");
const errorMessage = document.createElement("p"); // For error messages
errorMessage.id = "errorMessage";
document.querySelector("#weather_container").appendChild(errorMessage);

// Fetching weather data for default city
fetchWeatherData(defaultCity);

// Event listener for search button click
button.addEventListener("click", async () => {
  const cityName = searchInput.value.trim();

  if (!cityName) {
    errorMessage.textContent = "Please enter a city name!";
    searchInput.classList.add("error");
    setTimeout(() => {
      searchInput.classList.remove("error");
      errorMessage.textContent = "";
    }, 2000);
    return;
  }

  fetchWeatherData(cityName);
});

// Function to fetch weather data from API

async function fetchWeatherData(cityName) {
  try {
    const response = await fetch(
      `${apiUrl}?q=${cityName}&units=metric&appid=${apiKey}`
    );
    if (!response.ok) throw new Error("City not found!");
    const data = await response.json();

    // Send a request to the backend to store the weather data in the database
    const backendResponse = await fetch("connection.php?q=" + cityName);
    if (!backendResponse.ok)
      throw new Error("Failed to store data in backend!");

    displayWeather(data);
    errorMessage.textContent = "";
  } catch (error) {
    errorMessage.textContent = error.message;
  }
}

// Function to display fetched weather data
function displayWeather(data) {
  const { name, main, weather, wind, timezone } = data;

  // Update city name
  cityText.textContent = `${name}`;

  // Calculate local time in the city's timezone
  const localTime = new Date(Date.now() + timezone * 1000);

  // Format the date for the city's timezone
  const options = {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  };
  date.textContent = new Intl.DateTimeFormat("en-US", { ...options }).format(
    localTime
  );

  // Populate weather details
  weather_con.textContent = `Weather: ${weather[0].main}`;
  weather_description.textContent = `Weather Condition: ${weather[0].description}`;
  weather_icon.src = `https://openweathermap.org/img/wn/${weather[0].icon}@2x.png`;
  weather_icon.alt = weather[0].description;
  Temperature.textContent = `${main.temp}°C`;
  Pressure.textContent = `Pressure: ${main.pressure} hPa`;
  Humidity.textContent = `Humidity: ${main.humidity}%`;
  Wind.textContent = `Wind Speed: ${wind.speed} m/s`;
  direction.textContent = `Wind Direction: ${wind.deg}°`;
}
