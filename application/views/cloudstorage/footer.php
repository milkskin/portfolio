<script>
$(function () {
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
		.done(function (d) {
			data.node.a_attr.href = parent.a_attr.href + d.text + "/"
			inst.set_id(data.node, d.id)
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
		.done(function (d) {
			data.node.a_attr.href = parent.a_attr.href + d.text + "/"
			inst.set_type(data.node, "default")
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
		.done(function (d) {
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
		.done(function (d) {
			data.node.a_attr.href = new_parent.a_attr.href + d.text + "/"

			$.each(data.node.children_d, function (idx, value) {
				var obj = inst.get_node(value)
				var obj_parent = inst.get_node(obj.parent)

				obj.a_attr.href = obj_parent.a_attr.href + encodeURIComponent(obj.text) + "/"
			})

			inst.set_type(data.node, "default")
		})
	})
	.jstree({
		"core" : {
			"check_callback" : function (operation, node, node_parent, node_position, more) {
				// prevent rename at root node triggered by F2 key
				return (node_parent.id !== "#")
			},
			"data" : <?php echo $dir_list_json . PHP_EOL; ?>,
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
})
</script>
