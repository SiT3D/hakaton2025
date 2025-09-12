<template>
  <div>
    <div ref="map" class="map"></div>

    <div class="polygons" v-if="polygons.length">
      <h3>Выделенные участки</h3>
      <div v-for="(poly, index) in polygons" :key="index" class="polygon-item">
        <pre>{{ poly.coords }}</pre>
        <button @click="removePolygon(index)">✖</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue"

const map = ref(null)
const polygons = ref([])

function removePolygon(index) {
  // убрать с карты
  polygons.value[index].overlay.setMap(null)
  // убрать из массива
  polygons.value.splice(index, 1)
}

onMounted(() => {
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

  google.maps.event.addListener(drawingManager, "overlaycomplete", (event) => {
    if (event.type === "polygon") {
      const path = event.overlay.getPath().getArray()
      const coords = path.map((p) => ({
        lat: p.lat(),
        lng: p.lng(),
      }))

      polygons.value.push({
        overlay: event.overlay, // чтобы потом удалить
        coords,
      })
    }
  })
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
