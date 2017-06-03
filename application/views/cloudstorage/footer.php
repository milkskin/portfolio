<script>
$(function () {
<?php if ($error_alert): ?>
	$("#dialog").dialog({
		"resizable" : false,
		"width" : 400,
		"modal" : true
	})

<?php endif ?>
	$("#jstree_demo_div")
	.on("create_node.jstree", function (e, data) {
		var inst = data.instance
		var parent = inst.get_node(data.parent)
		var newnode_text = data.node.text

		inst.set_type(data.node, "temporary")
		inst.set_text(data.node, "Creating ...")
		inst.open_node(parent)

		$.post("/storagetree?operation=create_node", {
			"parent_uri" : parent.a_attr.href,
			"text" : newnode_text
		})
		.fail(function () {
			inst.refresh()
		})
		.done(function () {
			data.node.a_attr.href = parent.a_attr.href + encodeURIComponent(newnode_text) + "/"
			inst.set_text(data.node, newnode_text)
			inst.set_type(data.node, "default")
			inst.edit(data.node)
		})
	})
	.on("rename_node.jstree", function (e, data) {
		if (data.text === data.old) {
			return false
		}

		var inst = data.instance
		var parent = inst.get_node(data.node.parent)

		inst.set_type(data.node, "temporary")

		$.post("/storagetree?operation=rename_node", {
			"parent_uri" : parent.a_attr.href,
			"new_text" : data.text,
			"old_text" : data.old
		})
		.fail(function () {
			inst.refresh()
		})
		.done(function () {
			data.node.a_attr.href = parent.a_attr.href + encodeURIComponent(data.text) + "/"
			inst.set_type(data.node, "default")
			inst.redraw(true)
		})
	})
	.on("delete_node.jstree", function (e, data) {
		var inst = data.instance

		$.post("/storagetree?operation=delete_node", {
			"uri" : data.node.a_attr.href
		})
		.fail(function () {
			inst.refresh()
		})
		.done(function () {
		})
	})
	.on("move_node.jstree", function (e, data) {
		var inst = data.instance
		var new_parent = inst.get_node(data.parent)
		var old_parent = inst.get_node(data.old_parent)

		inst.set_type(data.node, "temporary")

		$.post("/storagetree?operation=move_node", {
			"new_parent_uri" : new_parent.a_attr.href,
			"old_parent_uri" : old_parent.a_attr.href,
			"text" : data.node.text
		})
		.fail(function () {
			inst.refresh()
		})
		.done(function () {
			var descendant = data.node.children.slice()

			data.node.a_attr.href = new_parent.a_attr.href + encodeURIComponent(data.node.text) + "/"

			for (var idx = 0; idx < descendant.length; idx += 1) {
				var value = descendant[idx]
				var obj = inst.get_node(value)
				var obj_parent = inst.get_node(obj.parent)

				obj.a_attr.href = obj_parent.a_attr.href + encodeURIComponent(obj.text) + "/"

				$.each(obj.children, function (i, v) {
					descendant.push(v)
				})
			}

			inst.set_type(data.node, "default")
			inst.redraw(true)
		})
	})
	.on("copy_node.jstree", function (e, data) {
		var inst = data.instance
		var new_parent = inst.get_node(data.parent)
		var old_parent = inst.get_node(data.old_parent)

		inst.set_type(data.node, "temporary")

		$.post("/storagetree?operation=copy_node", {
			"new_parent_uri" : new_parent.a_attr.href,
			"old_parent_uri" : old_parent.a_attr.href,
			"text" : data.node.text
		})
		.fail(function () {
			inst.refresh()
		})
		.done(function () {
			var descendant = data.node.children.slice()

			data.node.a_attr.href = new_parent.a_attr.href + encodeURIComponent(data.node.text) + "/"

			for (var idx = 0; idx < descendant.length; idx += 1) {
				var value = descendant[idx]
				var obj = inst.get_node(value)
				var obj_parent = inst.get_node(obj.parent)

				obj.a_attr.href = obj_parent.a_attr.href + encodeURIComponent(obj.text) + "/"

				$.each(obj.children, function (i, v) {
					descendant.push(v)
				})
			}

			inst.set_type(data.node, "default")
			inst.redraw(true)
		})
	})
	.jstree({
		"core" : {
			"check_callback" : function (operation, node, node_parent, node_position, more) {
				// prevent rename at root node triggered by F2 key
				return (node_parent.id !== "#")
			},
			"data" : <?php echo $dir_list_json; ?>,
			"multiple" : false
		},
		"plugins" : [
			"contextmenu", "dnd", "types", "unique"
		],
		"contextmenu" : {
			"items" : {
				"create" : {
					"separator_before" : false,
					"separator_after" : true,
					"_disabled" : function (data) {
						var inst = $.jstree.reference(data.reference)
						var obj = inst.get_node(data.reference)

						return (obj.type === "temporary")
					},
					"label" : "Create",
					"title" : "Create",
					"action" : function (data) {
						var inst = $.jstree.reference(data.reference)
						var obj = inst.get_node(data.reference)

						inst.create_node(obj, {}, "last", function (new_node) {
							try {
								inst.edit(new_node)
							}
							catch (ex) {
								setTimeout(function () {
									inst.edit(new_node)
								}, 0)
							}
						})
					},
					"icon" : "glyphicon glyphicon-plus",
				},
				"rename" : {
					"separator_before" : false,
					"separator_after" : false,
					"_disabled" : function (data) {
						var inst = $.jstree.reference(data.reference)
						var obj = inst.get_node(data.reference)

						return (obj.type === "temporary" || obj.type === "root")
					},
					"label" : "Rename",
					"title" : "Rename",
					"action" : function (data) {
						var inst = $.jstree.reference(data.reference)
						var obj = inst.get_node(data.reference)

						inst.edit(obj)
					},
					"icon" : "glyphicon glyphicon-pencil",
				},
				"remove" : {
					"separator_before" : false,
					"separator_after" : false,
					"_disabled" : function (data) {
						var inst = $.jstree.reference(data.reference)
						var obj = inst.get_node(data.reference)

						return (obj.type === "temporary" || obj.type === "root")
					},
					"label" : "Delete",
					"title" : "Delete",
					"action" : function (data) {
						var inst = $.jstree.reference(data.reference)
						var obj = inst.get_node(data.reference)

						if (inst.is_selected(obj)) {
							inst.delete_node(inst.get_selected())
						}
						else {
							inst.delete_node(obj)
						}
					},
					"icon" : "glyphicon glyphicon-trash",
				},
				"cut" : {
					"separator_before" : true,
					"separator_after" : false,
					"_disabled" : function (data) {
						var inst = $.jstree.reference(data.reference)
						var obj = inst.get_node(data.reference)

						return (obj.type === "temporary" || obj.type === "root")
					},
					"label" : "Cut",
					"title" : "Cut",
					"action" : function (data) {
						var inst = $.jstree.reference(data.reference)
						var obj = inst.get_node(data.reference)

						if (inst.is_selected(obj)) {
							inst.cut(inst.get_top_selected())
						}
						else {
							inst.cut(obj)
						}
					},
					"icon" : "glyphicon glyphicon-scissors",
				},
				"copy" : {
					"separator_before" : false,
					"separator_after" : false,
					"_disabled" : function (data) {
						var inst = $.jstree.reference(data.reference)
						var obj = inst.get_node(data.reference)

						return (obj.type === "temporary" || obj.type === "root")
					},
					"label" : "Copy",
					"title" : "Copy",
					"action" : function (data) {
						var inst = $.jstree.reference(data.reference)
						var obj = inst.get_node(data.reference)

						if (inst.is_selected(obj)) {
							inst.copy(inst.get_top_selected())
						}
						else {
							inst.copy(obj)
						}
					},
					"icon" : "glyphicon glyphicon-duplicate",
				},
				"paste" : {
					"separator_before" : false,
					"separator_after" : false,
					"_disabled" : function (data) {
						var inst = $.jstree.reference(data.reference)
						var obj = inst.get_node(data.reference)

						return (obj.type === "temporary" || ! inst.can_paste())
					},
					"label" : "Paste",
					"title" : "Paste",
					"action" : function (data) {
						var inst = $.jstree.reference(data.reference)
						var obj = inst.get_node(data.reference)

						inst.paste(obj)
					},
					"icon" : "glyphicon glyphicon-paste",
				}
			}
		},
		"types" : {
			"#" : {
				"valid_children" : ["root"]
			},
			"root" : {
				"valid_children" : ["default"]
			},
			"default" : {
				"valid_children" : ["default"]
			},
			"temporary" : {
				"valid_children" : []
			}
		}
	})

	// Initialize the jQuery File Upload widget:
	$("#fileupload").fileupload({
		// Uncomment the following to send cross-domain cookies:
		// "xhrFields" : { "withCredentials" : true },
		"url" : "storagedata"
	})

	// Enable iframe cross-domain access via redirect option:
	$("#fileupload").fileupload(
		"option",
		"redirect",
		window.location.href.replace(
			/\/[^\/]*$/,
			"/cors/result.html?%s"
		)
	)

	// Load existing files:
	$("#fileupload").addClass("fileupload-processing")
	$.ajax({
		// Uncomment the following to send cross-domain cookies:
		// "xhrFields" : { "withCredentials" : true },
		"url" : $("#fileupload").fileupload("option", "url"),
		"dataType" : "json",
		"context" : $("#fileupload")[0]
	})
	.done(function (result) {
		$(this).fileupload("option", "done")
		.call(this, $.Event("done"), { "result" : result })
	})
	.always(function () {
		$(this).removeClass("fileupload-processing")
	})
})
</script>
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
	<tr class="template-upload fade">
		<td>
			<span class="preview"></span>
		</td>
		<td>
			<p class="name">{%=file.name%}</p>
			<strong class="error text-danger"></strong>
		</td>
		<td>
			<p class="size">Processing...</p>
			<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
		</td>
		<td>
			{% if (!i && !o.options.autoUpload) { %}
				<button class="btn btn-primary start" disabled>
					<i class="glyphicon glyphicon-upload"></i>
					<span>Start</span>
				</button>
			{% } %}
			{% if (!i) { %}
				<button class="btn btn-warning cancel">
					<i class="glyphicon glyphicon-ban-circle"></i>
					<span>Cancel</span>
				</button>
			{% } %}
		</td>
	</tr>
{% } %}
</script>
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
	<tr class="template-download fade">
		<td>
			<span class="preview">
				{% if (file.thumbnailUrl) { %}
					<a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
				{% } %}
			</span>
		</td>
		<td>
			<p class="name">
				{% if (file.url) { %}
					<a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
				{% } else { %}
					<span>{%=file.name%}</span>
				{% } %}
			</p>
			{% if (file.error) { %}
				<div><span class="label label-danger">Error</span> {%=file.error%}</div>
			{% } %}
		</td>
		<td>
			<span class="size">{%=o.formatFileSize(file.size)%}</span>
		</td>
		<td>
			{% if (file.deleteUrl) { %}
				<button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
					<i class="glyphicon glyphicon-trash"></i>
					<span>Delete</span>
				</button>
				<input type="checkbox" name="delete" value="1" class="toggle">
			{% } else { %}
				<button class="btn btn-warning cancel">
					<i class="glyphicon glyphicon-ban-circle"></i>
					<span>Cancel</span>
				</button>
			{% } %}
		</td>
	</tr>
{% } %}
</script>
