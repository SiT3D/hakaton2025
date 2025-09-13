<template>
  <div>
    <div ref="map" class="map"></div>

    <div class="polygons" v-if="polygons.length">
      <h3>Выделенные участки</h3>
      <div v-for="(poly, index) in polygons" :key="index" class="polygon-item">
        <div>
          <b>Площадь:</b> {{ poly.area }} га
          <pre>{{ poly.coords }}</pre>
        </div>
        <button @click="removePolygon(index)">✖</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue"

const props = defineProps({
  coords: { type: Array, default: () => [] }
})
const emit = defineEmits(["update:coords"])

const map = ref(null)
const polygons = ref([])

function removePolygon(index) {
  polygons.value[index].overlay.setMap(null)
  polygons.value.splice(index, 1)

  const merged = polygons.value.map(p => p.coords)
  emit("update:coords", merged.flat())
}

onMounted(async () => {
  const gmap = new google.maps.Map(map.value, {
    center: { lat: 47.0105, lng: 28.8638 }, // Кишинёв
    zoom: 10,
  })

  const drawingManager = new google.maps.drawing.DrawingManager({
    drawingMode: google.maps.drawing.OverlayType.POLYGON,
    drawingControl: true,
    drawingControlOptions: {
      position: google.maps.ControlPosition.TOP_CENTER,
      drawingModes: ["polygon"],
    },
  })
  drawingManager.setMap(gmap)

  // ручное рисование
  google.maps.event.addListener(drawingManager, "overlaycomplete", (event) => {
    if (event.type === "polygon") {
      const path = event.overlay.getPath().getArray()
      const coords = path.map((p) => [p.lng(), p.lat()])

      const areaMeters = google.maps.geometry.spherical.computeArea(path)
      const areaHectares = (areaMeters / 10000).toFixed(2)

      polygons.value.push({
        overlay: event.overlay,
        coords,
        area: areaHectares,
      })

      const merged = polygons.value.map(p => p.coords)
      emit("update:coords", merged.flat())
    }
  })

  // загружаем плоты из API и рисуем их (без добавления в список)
  try {
    const res = await fetch("http://localhost:8085/plots-all?with_geo=1")
    const plots = await res.json()

    plots.forEach(plot => {
      if (plot.geometry?.coordinates) {
        const coords = plot.geometry.coordinates[0].map(([lng, lat]) => ({ lat, lng }))

        const polygon = new google.maps.Polygon({
          paths: coords,
          strokeColor: "#FF0000",
          strokeOpacity: 0.8,
          strokeWeight: 2,
          fillColor: "#FF0000",
          fillOpacity: 0.35,
        })
        polygon.setMap(gmap)

        const info = new google.maps.InfoWindow({
          content: `<b>${plot.name || "Участок"}</b><br/>${plot.area} га`,
        })
        polygon.addListener("click", (e) => {
          info.setPosition(e.latLng)
          info.open(gmap)
        })
      }
    })
  } catch (e) {
    console.error("Ошибка загрузки плотов", e)
  }
})
</script>

<style scoped>
.map {
  width: 100%;
  height: 500px;
  margin-bottom: 20px;
}
.polygons {
  font-family: monospace;
}
.polygon-item {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  border: 1px solid #ddd;
  border-radius: 6px;
  padding: 8px;
  margin-bottom: 10px;
  background: #fafafa;
}
.polygon-item pre {
  margin: 0;
  font-size: 12px;
  white-space: pre-wrap;
  word-break: break-all;
}
.polygon-item button {
  border: none;
  background: #e74c3c;
  color: white;
  font-weight: bold;
  border-radius: 4px;
  cursor: pointer;
  padding: 4px 8px;
}
</style>
