@extends('layouts.editor')

@section('title', 'Template Editor')

@push('styles')
<link href="{{ asset('css/admin/template-editor.css') }}" rel="stylesheet">
@endpush

@push('header-actions')
<!-- Unsaved Changes Indicator -->
<span x-show="hasUnsavedChanges && !saving" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-yellow-100 text-yellow-800 rounded-lg text-sm font-medium">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <span>Unsaved changes</span>
</span>

<!-- Saving Indicator -->
<span x-show="saving" class="text-sm text-gray-500 saving-indicator">Saving...</span>

<!-- Last Saved -->
<span x-show="lastSaved && !saving && !hasUnsavedChanges" class="text-sm text-gray-500">
    Last saved: <span x-text="formatTime(lastSaved)"></span>
</span>

<!-- Publish Button -->
<button @click="publish()" :disabled="templateData?.is_active"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition text-sm font-medium"
        :class="templateData?.is_active ? 'bg-green-500 text-white' : 'bg-black text-white hover:bg-gray-800'">
    <span x-text="templateData?.is_active ? 'Active' : 'Publish'"></span>
</button>
@endpush

@section('content')
<script>
    window.templateId = {{ $template->id ?? 0 }};
</script>

<div x-data="templateEditor()" x-init="init()" class="h-full flex">
    <!-- Editor Container -->
    <div id="editor-container" class="flex flex-1 overflow-hidden">
        <!-- Left Sidebar - Components -->
        <div class="components-sidebar bg-white">
            <div class="p-4 border-b sticky top-0 bg-white z-10">
                <h2 class="font-semibold text-gray-900">Components</h2>
                <p class="text-xs text-gray-500 mt-1">Sections contain elements</p>
            </div>
            <div class="p-3 space-y-2">
                <!-- Sections Category -->
                <div x-data="{ open: true }" class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="open = !open"
                            class="w-full flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 transition">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                            </svg>
                            <span class="font-medium text-sm text-gray-900">Sections</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-600 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="p-2 space-y-1">
                        <template x-for="(schema, type) in window.componentSchemas" :key="type">
                            <div x-show="schema.type === 'section'"
                                 draggable="true"
                                 class="component-item flex items-center gap-3 p-2 rounded-lg hover:bg-yellow-50 border border-transparent hover:border-yellow-400 cursor-grab active:cursor-grabbing transition"
                                 @click="window.templateEditorAddComponent(type)"
                                 @dragstart="window.templateEditorDragStart(type, 'section', $event)">
                                <div class="w-8 h-8 rounded bg-yellow-50 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path x-show="type === 'section_one_col'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5z"/>
                                        <path x-show="type === 'section_two_col'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/>
                                        <path x-show="type === 'section_three_col'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2m0 0V7a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-sm text-gray-900 truncate" x-text="schema.name"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Elements Category -->
                <div x-data="{ open: true }" class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="open = !open"
                            class="w-full flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 transition">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                            </svg>
                            <span class="font-medium text-sm text-gray-900">Elements</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-600 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="p-2 space-y-1">
                        <template x-for="(schema, type) in window.componentSchemas" :key="type">
                            <div x-show="schema.type === 'element'"
                                 draggable="true"
                                 class="component-item flex items-center gap-3 p-2 rounded-lg hover:bg-yellow-50 border border-transparent hover:border-yellow-400 cursor-grab active:cursor-grabbing transition"
                                 @click="window.templateEditorAddComponent(type, selectedSection?.id)"
                                 @dragstart="window.templateEditorDragStart(type, 'element', $event)">
                                <div class="w-8 h-8 rounded bg-yellow-50 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path x-show="type === 'text'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                        <path x-show="type === 'image'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-sm text-gray-900 truncate" x-text="schema.name"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Center - Canvas -->
        <div class="canvas-area">
            <div x-show="loading" class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-yellow-500"></div>
                <p class="mt-4 text-gray-600">Loading editor...</p>
            </div>

            <!-- Canvas - Always visible (no empty state per user request) -->
            <div id="editor-canvas" x-show="!loading"
                 @click.self="selectTemplate()"
                 @dragover.prevent="isDraggingOver = true; $event.dataTransfer.dropEffect = 'copy'"
                 @dragleave="isDraggingOver = false"
                 @drop="onCanvasDrop($event); isDraggingOver = false"
                 :class="['viewport-' + currentViewport, { 'drag-over': isDraggingOver }]"
                 class="bg-white min-h-screen p-4 transition-colors duration-200">

                <!-- Render nested sections (top-level only, children rendered inline) -->
                <template x-for="(section, index) in sections" :key="section.id">
                    <div class="section-wrapper"
                         :data-section-id="section.id"
                         :class="{ 'selected': selectedSection?.id === section.id }"
                         @click="selectSection(section)">

                        <!-- Section actions -->
                        <div class="section-actions">
                            <button @click.stop="moveSection(index, -1)"
                                    x-show="index > 0"
                                    class="p-1.5 bg-white rounded shadow hover:bg-gray-50"
                                    title="Move up">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                </svg>
                            </button>
                            <button @click.stop="moveSection(index, 1)"
                                    x-show="index < sections.length - 1"
                                    class="p-1.5 bg-white rounded shadow hover:bg-gray-50"
                                    title="Move down">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <button @click.stop="duplicateSection(section)"
                                    class="p-1.5 bg-white rounded shadow hover:bg-gray-50"
                                    title="Duplicate">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                            <button @click.stop="confirmDeleteSection(section)"
                                    class="p-1.5 bg-red-500 text-white rounded shadow hover:bg-red-600"
                                    title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Section preview - Live rendering with nested children support -->
                        <div class="section-preview bg-white border-2 rounded-lg overflow-hidden"
                             :style="getSectionStyle(section)">

                            <!-- SECTION TYPE: Container (section_one_col, section_two_col, section_three_col) -->
                            <template x-if="componentSchemas[section.section_type]?.type === 'section'">
                                <!-- Render section container with children -->
                                <div :style="getSectionContainerStyle(section)" class="section-container">
                                    <!-- Section label when in editor -->
                                    <div class="text-xs text-gray-400 mb-2 flex items-center gap-2" x-show="selectedSection?.id === section.id">
                                        <span x-text="componentSchemas[section.section_type]?.name"></span>
                                        <span class="text-gray-300">|</span>
                                        <span class="text-gray-400">Drop elements here</span>
                                    </div>

                                    <!-- Render children based on section type -->
                                    <!-- 1 Column Section -->
                                    <template x-if="section.section_type === 'section_one_col'">
                                        <div class="section-drop-zone min-h-[50px] p-2"
                                             data-column-index="0"
                                             :class="{ 'bg-yellow-50 border-2 border-dashed border-yellow-300': selectedSection?.id === section.id && section.children?.length === 0 }">
                                            <template x-for="(element, elemIndex) in (section.children || [])" :key="element.id">
                                                <div class="element-wrapper relative border border-gray-200 rounded p-2 mb-2 hover:border-yellow-400"
                                                     :data-element-id="element.id"
                                                     :class="{ 'selected': selectedSection?.id === element.id }"
                                                     @click.stop="selectSection(element)">
                                                    <!-- Render element content -->
                                                    <div x-html="renderElement(element)"></div>

                                                    <!-- Element actions when selected -->
                                                    <div class="absolute top-1 right-1 flex gap-1 opacity-0 hover:opacity-100"
                                                         :class="{ 'opacity-100': selectedSection?.id === element.id }">
                                                        <button @click.stop="confirmDeleteElement(element, section)"
                                                                class="p-1 bg-red-500 text-white rounded text-xs hover:bg-red-600"
                                                                title="Delete">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- 2 Column Section -->
                                    <template x-if="section.section_type === 'section_two_col'">
                                        <div class="grid gap-2"
                                             :style="{ gridTemplateColumns: getSectionColumnRatio(section) }">
                                            <template x-for="(colIndex) in 2" :key="colIndex">
                                                <div class="section-drop-zone min-h-[50px] p-2 border border-dashed border-gray-200 rounded"
                                                     :data-column-index="colIndex"
                                                     :class="{ 'bg-yellow-50 border-yellow-300': selectedSection?.id === section.id }">
                                                    <template x-for="(element, elemIndex) in (section.children || [])" :key="element.id">
                                                        <div x-show="element.order_index === colIndex"
                                                             class="element-wrapper relative border border-gray-200 rounded p-2 mb-2 hover:border-yellow-400"
                                                             :data-element-id="element.id"
                                                             :class="{ 'selected': selectedSection?.id === element.id }"
                                                             @click.stop="selectSection(element)">
                                                            <div x-html="renderElement(element)"></div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- 3 Column Section -->
                                    <template x-if="section.section_type === 'section_three_col'">
                                        <div class="grid gap-2" style="grid-template-columns: repeat(3, 1fr);">
                                            <template x-for="(colIndex) in 3" :key="colIndex">
                                                <div class="section-drop-zone min-h-[50px] p-2 border border-dashed border-gray-200 rounded"
                                                     :data-column-index="colIndex"
                                                     :class="{ 'bg-yellow-50 border-yellow-300': selectedSection?.id === section.id }">
                                                    <template x-for="(element, elemIndex) in (section.children || [])" :key="element.id">
                                                        <div x-show="element.order_index === colIndex"
                                                             class="element-wrapper relative border border-gray-200 rounded p-2 mb-2 hover:border-yellow-400"
                                                             :data-element-id="element.id"
                                                             :class="{ 'selected': selectedSection?.id === element.id }"
                                                             @click.stop="selectSection(element)">
                                                            <div x-html="renderElement(element)"></div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- ELEMENT TYPE: Direct render (text, image, etc.) -->
                            <template x-if="componentSchemas[section.section_type]?.type === 'element'">
                                <div class="p-6">
                                    <div x-html="renderElement(section)"></div>
                                </div>
                            </template>

                            <!-- Unknown type fallback -->
                            <template x-if="!componentSchemas[section.section_type]">
                                <div class="p-4 text-center text-gray-500">
                                    <p class="font-medium" x-text="section.section_type"></p>
                                    <p class="text-sm">(Unknown component type)</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Empty state hint when canvas is completely empty -->
                <div x-show="sections.length === 0"
                     class="flex flex-col items-center justify-center min-h-[400px] text-gray-400">
                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0h6"/>
                    </svg>
                    <p class="text-lg font-medium">Canvas is ready</p>
                    <p class="text-sm">Click Sections or Elements in the sidebar to add components</p>
                </div>
            </div>
        </div>

        <!-- Right - Properties Panel -->
        <div class="properties-panel bg-white">
            <!-- STATE 1: No Selection → Template Properties -->
            <div x-show="!selectedSection" class="p-6">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                    </svg>
                    Template Settings
                </h3>
                <div class="space-y-4">
                    <p class="text-sm text-gray-500">Click on canvas background or select a section/element to see its properties.</p>

                    <div class="border-t pt-4 mt-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Template Information</h4>
                        <div class="text-sm text-gray-600">
                            <p><strong>Name:</strong> <span x-text="templateData?.name || '-'"></span></p>
                            <p><strong>Status:</strong>
                                <span x-show="templateData?.is_active" class="text-green-600">Active</span>
                                <span x-show="!templateData?.is_active" class="text-gray-500">Draft</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STATE 2 & 3: Section or Element Selected -->
            <div x-show="selectedSection">
                <!-- Header with type indicator -->
                <div class="px-4 py-3 border-b bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900" x-text="componentSchemas[selectedSection?.section_type]?.name"></h3>
                        <span class="text-xs px-2 py-1 rounded"
                              :class="{
                                'bg-blue-100 text-blue-700': componentSchemas[selectedSection?.section_type]?.type === 'section',
                                'bg-green-100 text-green-700': componentSchemas[selectedSection?.section_type]?.type === 'element'
                              }"
                              x-text="componentSchemas[selectedSection?.section_type]?.type === 'section' ? 'Section' : 'Element'">
                        </span>
                    </div>
                </div>

                <!-- Tabs (if component has tabs defined, otherwise show Settings) -->
                <div class="flex border-b sticky top-0 bg-white z-10">
                    <template x-for="tab in (componentSchemas[selectedSection?.section_type]?.tabs || ['Settings'])" :key="tab">
                        <button @click="currentTab = tab"
                                :class="{ 'active': currentTab === tab }"
                                class="property-tab flex-1 px-4 py-3 text-sm font-medium"
                                x-text="tab">
                        </button>
                    </template>
                </div>

                <!-- Properties -->
                <div class="p-4">
                    <!-- Dynamic properties based on schema -->
                    <div class="space-y-4">
                        <template x-for="(field, fieldKey) in componentSchemas[selectedSection?.section_type]?.fields" :key="fieldKey">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1" x-text="field.label"></label>

                                <!-- Text input -->
                                <template x-if="field.type === 'text'">
                                    <input type="text"
                                           :value="selectedSection?.props?.[fieldKey]"
                                           @input="updateProp(fieldKey, $event.target.value)"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                                </template>

                                <!-- Textarea -->
                                <template x-if="field.type === 'textarea'">
                                    <textarea rows="4"
                                              @input="updateProp(fieldKey, $event.target.value)"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                              x-text="selectedSection?.props?.[fieldKey] || ''"></textarea>
                                </template>

                                <!-- Select dropdown -->
                                <template x-if="field.type === 'select'">
                                    <select :value="selectedSection?.props?.[fieldKey]"
                                            @change="updateProp(fieldKey, $event.target.value)"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                                        <template x-for="option in field.options" :key="option.value">
                                            <option :value="option.value" x-text="option.label"></option>
                                        </template>
                                    </select>
                                </template>

                                <!-- Number input -->
                                <template x-if="field.type === 'number'">
                                    <input type="number"
                                           :value="selectedSection?.props?.[fieldKey]"
                                           @input="updateProp(fieldKey, parseInt($event.target.value))"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                                </template>

                                <!-- Slider -->
                                <template x-if="field.type === 'slider'">
                                    <div class="space-y-2">
                                        <input type="range"
                                               :min="field.min || 0"
                                               :max="field.max || 100"
                                               :value="selectedSection?.props?.[fieldKey] || field.default || 0"
                                               @input="updateProp(fieldKey, parseInt($event.target.value))"
                                               class="w-full">
                                        <div class="flex justify-between text-xs text-gray-500">
                                            <span x-text="(selectedSection?.props?.[fieldKey] || field.default || 0) + (field.unit || '')"></span>
                                            <span x-text="(field.max || 100) + (field.unit || '')"></span>
                                        </div>
                                    </div>
                                </template>

                                <!-- Color picker -->
                                <template x-if="field.type === 'color'">
                                    <div class="flex items-center gap-2">
                                        <input type="color"
                                               :value="selectedSection?.props?.[fieldKey] || field.default || '#000000'"
                                               @input="updateProp(fieldKey, $event.target.value)"
                                               class="w-12 h-10 px-1 py-1 border border-gray-300 rounded-lg cursor-pointer">
                                        <input type="text"
                                               :value="selectedSection?.props?.[fieldKey] || field.default || '#000000'"
                                               @input="updateProp(fieldKey, $event.target.value)"
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono">
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- No properties message -->
                        <div x-show="!componentSchemas[selectedSection?.section_type]?.fields" class="text-sm text-gray-500 text-center py-4">
                            No properties available for this component type.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Drag & Drop Visual Feedback */
    #editor-canvas.drag-over {
        background-color: #fefce8 !important; /* yellow-50 */
        border: 2px dashed #d4af37 !important;
    }

    /* Section drop zone highlighting */
    .section-drop-zone {
        min-height: 60px;
        transition: all 0.2s ease;
    }

    .section-drop-zone.drag-over-element {
        background-color: #fefce8;
        border: 2px dashed #d4af37;
    }

    /* Component item being dragged */
    .component-item:active {
        opacity: 0.5;
    }

    /* Section wrapper hover state */
    .section-wrapper:hover .section-actions {
        opacity: 1;
    }

    .section-wrapper .section-actions {
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .section-wrapper.selected .section-actions {
        opacity: 1;
    }
</style>
@endpush

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="{{ asset('js/admin/template-editor.js') }}"></script>
@endsection
