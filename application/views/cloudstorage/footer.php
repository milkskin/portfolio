<script>
$(function () {
	$("#jstree_demo_div")
	.jstree({
		"core" : {
			"check_callback" : true,
			"data" : <?php echo $dir_list_json . PHP_EOL; ?>
		},
		"plugins" : [
			"contextmenu", "dnd", "types", "unique"
		],
		"contextmenu" : {
			"items" : {
				"create" : {
					"separator_before" : false,
					"separator_after" : true,
					"_disabled" : false,
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

						return (obj.type === "root")
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

						return (obj.type === "root")
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

						return (obj.type === "root")
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

						return (obj.type === "root")
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

						return ( ! inst.can_paste())
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
