<template>
  <div class="relative h-[550px] w-full overflow-hidden rounded-2xl border border-slate-200">
    <div ref="mapContainer" class="h-full w-full"></div>
    <div class="absolute bottom-4 left-4 z-[1000] rounded-xl border border-white/70 bg-white/90 px-3 py-2 text-[10px] font-semibold text-slate-600 shadow-sm backdrop-blur-sm">
      Leaflet 3D Map Pin • OpenStreetMap
    </div>
  </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted, watch, ref } from "vue";
import L from "leaflet";

const props = defineProps({
  activeLayers: {
    type: Object,
    required: true,
  },
  analyses: {
    type: Array,
    default: () => [],
  },
});

const mapContainer = ref(null);
let map;
let layers;

// Help map region name to approximate coordinates in Pantura
const getRegionCoords = (name, index) => {
  const normalized = name.toLowerCase();
  if (normalized.includes("sayung") || normalized.includes("demak")) return [-6.95, 110.46];
  if (normalized.includes("kaliwungu") || normalized.includes("kendal")) return [-6.91, 110.15];
  if (normalized.includes("tegal")) return [-6.86, 109.12];
  if (normalized.includes("brebes") || normalized.includes("randusanga")) return [-6.84, 109.04];
  if (normalized.includes("pekalongan")) return [-6.88, 109.67];
  if (normalized.includes("jepara")) return [-6.73, 110.67];
  
  // If unrecognized, distribute them in a nice cluster in Central Java coast (around Semarang/Demak)
  // to prevent overlapping
  const baseLat = -6.90;
  const baseLng = 110.20 + ((index % 10) * 0.15);
  return [baseLat, baseLng];
};

// Function to generate premium 3D HTML marker representation
const create3DMarkerIcon = (colorHex) => {
  return L.divIcon({
    className: "custom-3d-pin-wrapper",
    html: `
      <div class="relative flex items-center justify-center" style="width: 40px; height: 40px;">
        <!-- Pulsing Ring -->
        <span class="absolute inline-flex h-full w-full animate-ping rounded-full opacity-75" style="background-color: ${colorHex};"></span>
        
        <!-- 3D Push Pin Body -->
        <div class="relative flex flex-col items-center cursor-pointer transform hover:scale-110 transition-transform duration-200" style="filter: drop-shadow(0px 8px 6px rgba(0,0,0,0.3));">
          <!-- Pin Head (Spherical Look) -->
          <div class="w-7 h-7 rounded-full flex items-center justify-center border border-white/30" style="background: radial-gradient(circle at 35% 35%, ${colorHex} 0%, #1e293b 85%);">
            <!-- Dot center -->
            <div class="w-1.5 h-1.5 rounded-full bg-white opacity-80"></div>
          </div>
          <!-- Pin Stem -->
          <div class="w-1 bg-gradient-to-b from-slate-400 to-slate-800" style="height: 12px; margin-top: -2px;"></div>
          <!-- Pin Shadow Base -->
          <div class="w-3 h-1.5 bg-black/40 rounded-full blur-[1px]" style="margin-top: -1px;"></div>
        </div>
      </div>
    `,
    iconSize: [40, 52],
    iconAnchor: [20, 48],
    popupAnchor: [0, -48]
  });
};

const makeDetailedPopup = (name, province, area, risk, priority, carbon, info, weather, marine) => {
  const priorityBadge = priority === 'Tinggi' || priority === 'High' 
    ? 'bg-red-500 text-white font-bold' 
    : 'bg-amber-500 text-white font-bold';

  let realtimeHtml = '';
  if (weather || marine) {
    realtimeHtml = `
      <div class="mt-2.5 border-t pt-2.5">
        <div class="text-[9px] font-bold uppercase tracking-[0.1em] text-primary mb-1.5 flex items-center gap-1">
          <span class="relative flex h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
          </span>
          Live Realtime (BMKG & Ocean)
        </div>
        <div class="grid grid-cols-2 gap-2 text-[10px]">
          ${weather && weather.temp ? `
            <div class="bg-slate-50 p-1.5 rounded border border-slate-100 flex flex-col justify-between">
              <div>
                <span class="text-slate-400 text-[8px] uppercase font-bold block leading-none mb-1">Cuaca</span>
                <span class="font-semibold text-slate-700 block truncate">${weather.desc || 'Cerah'}</span>
              </div>
              <span class="font-bold text-sky-600 text-xs mt-1 block">${weather.temp}°C</span>
            </div>
          ` : `
            <div class="bg-slate-50 p-1.5 rounded border border-slate-100 flex flex-col justify-center items-center text-slate-400 italic text-[8px]">
              No weather data
            </div>
          `}
          ${marine && marine.wave_height ? `
            <div class="bg-slate-50 p-1.5 rounded border border-slate-100 flex flex-col justify-between">
              <div>
                <span class="text-slate-400 text-[8px] uppercase font-bold block leading-none mb-1">Gelombang</span>
                <span class="font-semibold text-slate-700 block">${marine.wave_height} m</span>
              </div>
              <span class="text-[8px] text-slate-500 mt-1 block">Air: ${marine.water_temp ?? '-'}°C</span>
            </div>
          ` : `
            <div class="bg-slate-50 p-1.5 rounded border border-slate-100 flex flex-col justify-center items-center text-slate-400 italic text-[8px]">
              No marine data
            </div>
          `}
        </div>
      </div>
    `;
  }

  return `
    <div class="p-3 w-64 text-slate-800 font-sans" style="line-height: 1.4;">
      <div class="flex items-center justify-between border-b pb-2 mb-2">
        <div>
          <h4 class="font-bold text-sm text-slate-900">${name}</h4>
          <p class="text-[10px] text-slate-500 font-medium">${province}</p>
        </div>
        <span class="text-[9px] px-2 py-0.5 rounded ${priorityBadge}">
          ${priority}
        </span>
      </div>
      <div class="space-y-1.5 text-xs">
        <div class="flex justify-between">
          <span class="text-slate-500">Luas Area:</span>
          <span class="font-semibold">${area} ha</span>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-500">Skor Risiko Iklim:</span>
          <span class="font-bold text-sky-600">${risk}/100</span>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-500">Potensi Karbon:</span>
          <span class="font-semibold text-emerald-600">${carbon} tCO₂e</span>
        </div>
      </div>
      ${realtimeHtml}
      <div class="mt-3 bg-slate-50 p-2 rounded text-[10px] text-slate-600 italic border border-slate-100">
        "${info}"
      </div>
    </div>
  `;
};

const buildLayers = () => {
  const priorityLayer = L.layerGroup();
  const climateLayer = L.layerGroup();
  const carbonLayer = L.layerGroup();

  // 1. Add Dynamic DB Markers Only (No more hardcoded demo markers)
  props.analyses.forEach((item, index) => {
    const coords = getRegionCoords(item.region_name, index);
    const risk = item.climate_risk_score ?? 50;
    const priority = item.restoration_priority ?? 'Medium';
    const carbon = (item.carbon_potential ?? 0).toLocaleString("id-ID");

    // Dynamic marker color based on priority
    let color = "#0ea5e9"; // blue
    if (priority === 'Tinggi' || priority === 'High') {
      color = "#ef4444"; // red
    } else if (item.carbon_potential > 2000) {
      color = "#22c55e"; // green
    }

    const popupHtml = makeDetailedPopup(
      item.region_name,
      item.province ?? "Jawa Tengah",
      item.area_size ?? "0",
      risk,
      priority,
      carbon,
      `Hasil kalkulasi nyata database untuk wilayah ${item.region_name}.`,
      item.realtime_weather,
      item.realtime_marine
    );

    const marker = L.marker(coords, { icon: create3DMarkerIcon(color) }).bindPopup(popupHtml);

    if (priority === 'Tinggi' || priority === 'High') {
      marker.addTo(priorityLayer);
    } else if (risk > 60) {
      marker.addTo(climateLayer);
    } else {
      marker.addTo(carbonLayer);
    }
  });

  return {
    priority: priorityLayer,
    climate: climateLayer,
    carbon: carbonLayer,
  };
};

const syncLayers = () => {
  if (!map || !layers) return;

  const state = props.activeLayers;
  Object.entries(layers).forEach(([key, layer]) => {
    const enabled = Boolean(state[key]);
    if (enabled && !map.hasLayer(layer)) {
      layer.addTo(map);
    }
    if (!enabled && map.hasLayer(layer)) {
      map.removeLayer(layer);
    }
  });
};

onMounted(() => {
  map = L.map(mapContainer.value, {
    zoomControl: false,
  }).setView([-6.9, 110.2], 8);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
  }).addTo(map);

  L.control.zoom({ position: "bottomright" }).addTo(map);

  layers = buildLayers();
  syncLayers();
});

watch(
  () => props.activeLayers,
  () => {
    syncLayers();
  },
  { deep: true }
);

watch(
  () => props.analyses,
  () => {
    if (map && layers) {
      Object.values(layers).forEach(layer => {
        if (map.hasLayer(layer)) map.removeLayer(layer);
      });
      layers = buildLayers();
      syncLayers();
    }
  },
  { deep: true }
);

onBeforeUnmount(() => {
  if (map) {
    map.remove();
  }
});
</script>

<style>
/* Leaflet standard popup customization to look sleek and modern */
.leaflet-popup-content-wrapper {
  border-radius: 16px !important;
  padding: 0 !important;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
  border: 1px solid rgba(226, 232, 240, 0.8);
}
.leaflet-popup-content {
  margin: 0 !important;
}
.leaflet-popup-close-button {
  top: 10px !important;
  right: 10px !important;
  color: #64748b !important;
}
</style>
