# UI Guidelines

A guide to help in building bars, dropdowns, panels, fields and other UI elements within admin interface.

## Main structure

```
HEADER.hbar#topbar !+

	div.hbar-section
		div.flexrow
			div | form | input | button | a | span
	
NAV.hbar#navbar !+

	div.hbar-section
		details.dropdown
			summary
			{div|form}.dropdown-content
				{div|form}.dropdown-section
					h2.dropdown-section-title
					div.dropdown-section-content
						(p.help)
						p.field
							label
							input | button | a | span
						
MAIN
	
	? aside.panel
		(h1.panel-title)
		div.scrollable
			? {div|form}.panel-section
				h2.panel-section-title
				{div|form}.panel-section-content
					(.list | .tree | fieldset)
			? details
	
	? aside.collapsible
		label + input[type=checkbox]
		.collapsible-content
			(h1.panel-title)
			div.scrollable	
				{div|form}.panel-section
					h2.panel-section-title
					{div|form}.panel-section-content
						(.list | .tree | fieldset)
							(h3 | legend)
							(p.help)
							p.field
								label
								input | button | a
	
	? section.main#pages
		h1
			text
			div.flexrow
				input | button | a
		div#searchbar
			…
		div.scrollable	
			table
				…

	? section.main#page
		div.tabs
			div.tab
				label + input[type=checkbox]
				div.tab-content
					textarea

	? section.main#medias
		h1
		div.scrollable	
			table		

	? section.main#admin
		div.scrollable	
			div.admin-section
				h2.admin-section-title
				div.admin-section-content

	? section.main#users
		h1
		div.scrollable	
			table		

	? section.main#account
		div.scrollable	
			div.admin-section
				h2.admin-section-title
				div.admin-section-content

	? section.main#info
		div.scrollable	
```



## Elements

Label comes first, then comes the input. CSS manages the order in case of checkboxes or radios.

### Layout helpers

`.flexrow` helps to layout horizontal items, whichever is their inline/block natural behavior.

`.flexcol` is the same for vertical layouts.

### Fields

Each field (input, submit, checkbox or radio) is wrapped widthin a `<p class="field"></p>`.
```html
<p class="field">
	<label for="myinput">label</label>
	<input type="text" name="myinput" id="myinput" value="">
</p>
```
When two fields whould be in a row, wrap them in a `.flexrow`:
```html
<div class="flexrow">
	<p class="field">
		<label for="myinput">label</label>
		<input type="text" name="myinput" id="myinput" value="">
	</p>
	<p class="field">
		<label for="myinput2">label</label>
		<input type="text" name="myinput2" id="myinput2" value="">
	</p>
</div>
```
Submit inputs or buttons are wrapped in a  `<p class="field submit-field"></p>`.
```html
<p class="field submit-field">
	<input type="submit" value="submit">
</p>
```
### Horizontal bar

Used for main top-bar and dropwdowns menu-bar.

Should have the class `.hbar`, and contain one or many `.hbar-section`.

Each section should contain one or many `.flexrow` to properly layout any kind of children :
- `div`: for grouping elements – which might not be useful
- `form > input(s)`: for update/post actions
- `a`: for links actions
- `span`: for help info 
