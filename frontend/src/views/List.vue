<template>
  <div class="farm">
    <div class="header">
      <h1>Farm Plot</h1>
      <button class="add" @click="addPlot">+</button>
    </div>

    <div v-for="plot in plots" :key="plot.id" class="plot">
      <input type="checkbox" :value="plot.id" v-model="selected"/>

      <div class="plot-info">
        {{ plot.name }}
        <div class="dropdown">
          <button @click="toggleMenu(plot.id)" class="dots">...</button>
          <div v-if="openMenu === plot.id" class="menu">
            <button @click="change(plot)">Change</button>
            <button @click="exportOne(plot)">Export</button>
            <button @click="remove(plot)">Delete</button>
          </div>
        </div>
      </div>
    </div>

    <button v-if="selected.length > 1" class="export-all" @click="exportAll">
      export selected ({{ selected.length }})
    </button>
  </div>
</template>

<script setup>
import {ref} from "vue"

const plots = ref([
  {
    id: 1,
    name: "North Field sdfsd fsdf sdf sdf,ds 5sdfsdf3 sd dshf sdaf , sdfvsdefgsd fesd t..."
  },
  {
    id: 2,
    name: "South Plot, 23 ha, pasture"
  },
  {
    id: 3,
    name: "East Meadow, 12 ha, cattle"
  },
  {
    id: 4,
    name: "East Meadow, 13 ha, corn"
  },
  {
    id: 5,
    name: "East Meadow, 2 ha, straw..."
  },
  {
    id: 6,
    name: "East Meadow, 2 ha, straw..."
  },
  {
    id: 7,
    name: "East Meadow, 2 ha, straw..."
  },
  {
    id: 8,
    name: "East Meadow, 2 ha, straw..."
  },
  {
    id: 9,
    name: "East Meadow, 2 ha, straw..."
  },
  {
    id: 10,
    name: "East Meadow, 2 ha, straw..."
  },
  {
    id: 11,
    name: "East Meadow, 2 ha, straw..."
  },
  {
    id: 12,
    name: "East Meadow, 2 ha, straw..."
  },
])

const selected = ref([])
const openMenu = ref(null)

function toggleMenu(id)
{
  openMenu.value = openMenu.value === id ? null : id
}

function addPlot()
{
  console.log("Add new")
}

function change(plot)
{
  console.log("Change", plot)
}

function exportOne(plot)
{
  console.log("Export", plot)
}

function remove(plot)
{
  console.log("Delete", plot)
}

function exportAll()
{
  console.log("Export selected:", selected.value)
}
</script>

<style scoped>
.farm
{
  max-width: 500px;
  margin: 40px auto;
  font-family: sans-serif;
}

.header
{
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.add
{
  width: 40px;
  height: 40px;
  border: none;
  border-radius: 50%;
  background: black;
  color: white;
  font-size: 20px;
  cursor: pointer;
}

.dots
{
  border: none;
  background: none;
  font-size: 20px;
  cursor: pointer;
}

.plot
{
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 10px 0;
}

.plot-info
{
  flex: 1;
  border: 1px solid #ccc;
  padding: 8px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: relative;
}

.dropdown
{
  position: relative;
}

.dropdown button
{
  border: none;
  background: none;
  font-size: 20px;
  cursor: pointer;
}

.menu
{
  position: absolute;
  right: 0;
  top: 35px;
  background: white;
  border: 1px solid #ccc;
  display: flex;
  flex-direction: column;
  z-index: 10;
}

.menu button
{
  border: none;
  background: none;
  padding: 8px 12px;
  cursor: pointer;
  text-align: left;
}

.menu button:nth-child(1)
{
  color: green;
}

.menu button:nth-child(2)
{
  color: blue;
}

.menu button:nth-child(3)
{
  color: red;
}

.export-all
{
  display: block;
  position: fixed;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  margin: 20px auto;
  padding: 12px;
  background: black;
  color: white;
  border: none;
  cursor: pointer;
  font-weight: bold;
}

input[type="checkbox"]
{
  width: 20px;
  height: 20px;
  cursor: pointer;
}
</style>
