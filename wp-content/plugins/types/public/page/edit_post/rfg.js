;(function( $ ) {
    var Types = Types || {};
    Types.RepeatableGroup = {};
    Types.RepeatableGroup.Model = {}

    var ajaxInfo = $.parseJSON( WPV_Toolset.Utils.editor_decode64( $( '#types_rfg_model_data' ).html() ) ),
        lastActiveGroupPerLevel = {};

    /**
     * Function to update HTML of inputs related to user changes
     *
     * This is needed becauses otherwise we will lose user input after resorting. It's a problem combining sorting
     * with knockout. Because after sorting the element must be re-applied, therefore we must save the item before
     * sorting, otherwise knockout would use the original inputs.
     * The easiest way to save the user changes is to update the DOM.
     *
     * How to avoid this?
     * For this we need to get a proper rendering for the input fields, which should than be controlled by knockout.
     * Currently we just passing the full html of the field input to knockout.
     *
     * https://stackoverflow.com/questions/1388893/jquery-html-in-firefox-uses-innerhtml-ignores-dom-changes#1388965
     */
    var oldHTML = $.fn.html;
    $.fn.typesUpdateHtml = function() {
        if( arguments.length ) return oldHTML.apply( this, arguments );
        $( "input,button", this ).each( function() {
            this.setAttribute( 'value', this.value );
        } );
        $( "textarea", this ).each( function() {
            this.innerHTML = this.value;
        } );
        $( "input:radio,input:checkbox", this ).each( function() {
            if( this.checked ) this.setAttribute( 'checked', 'checked' );
            else this.removeAttribute( 'checked' );
        } );
        $( "option", this ).each( function() {
            if( this.selected ) this.setAttribute( 'selected', 'selected' );
            else this.removeAttribute( 'selected' );
        } );
        return oldHTML.apply( this );
    };

    /**
     * Group model
     *
     * @param data
     * @param level
     * @param field
     * @constructor
     */
    Types.RepeatableGroup.Model.Group = function( data, level, field ) {
        var self = this;

        self.id = data.id || 1;
        self.parent_post_id = data.parent_post_id || 0;
        self.title = data.title || '';
        self.level = level || 1;
        self.field = field || null;
        self.controlsActive = data.controlsActive || 0;
        self.wpmlIsTranslationModeSupported = data.wpmlIsTranslationModeSupported || 0;
        self.wpmlFilterExistsForOriginalData = data.wpmlFilterExistsForOriginalData || 0;
        self.visible = ko.observable( false );

        // Map Headlines
        self.headlines = ko.observableArray( ko.utils.arrayMap( data.headlines || [],
            function( headlineData ) {
                return new Types.RepeatableGroup.Model.Headline( headlineData );
            } ) );

        // Map Items
        self.items = ko.observableArray( ko.utils.arrayMap( data.items || [],
            function( itemData ) {
                return new Types.RepeatableGroup.Model.Item( itemData, self );
            } ) );

        /**
         * Toggle visibility
         */
        self.toggleGroupVisibility = function() {
            $( 'div.tooltip' ).remove(); // remove any tooltip

            self.visible( !self.visible() );

            // for vertical view
            if( self.visible && $( '.js-rgy' ).length ) {
                // only one group per level should be visible
                if( lastActiveGroupPerLevel[self.level] && lastActiveGroupPerLevel[self.level] != self  ) {
                    lastActiveGroupPerLevel[self.level].visible( false );
                }

                lastActiveGroupPerLevel[self.level] = self;
            }


            if( self.field === null ) {
                // nothing else to do for a non nested group
                return;
            }

            // nested group - count active groups (for rowspan)
            if( self.visible() ) {
                if( self.field.item.currentNestedActiveGroup !== null && self.field.item.currentNestedActiveGroup != self ) {
                    self.field.item.currentNestedActiveGroup.toggleGroupVisibility();
                }
                self.field.item.activeNestedGroups( self.field.item.activeNestedGroups() + 1 );
                self.field.item.currentNestedActiveGroup = self;
            } else {
                self.field.item.activeNestedGroups( self.field.item.activeNestedGroups() - 1 );
            }

            Types.RepeatableGroup.Functions.initLegacyFields();
            Types.RepeatableGroup.Functions.cssExtension();
        }

        self.startItemDeletion = function( item ) {
            item.startDeletionCountdown();
        }

        /**
         * Remove item from group
         * @param data
         */
        self.removeItem = function( item ) {
            $.ajax( {
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: ajaxInfo.action.name,
                    wpnonce: ajaxInfo.action.nonce,
                    remove_id: item.id,
                    belongs_to_post_id: item.group.parent_post_id,
                    repeatable_group_action: 'json_repeatable_group_remove_item',
                },
                dataType: 'json',
                success: function( response ) {
                    if( response.success ) {
                        self.items.remove( item );
                        if( self.items().length == 0 ) {
                            self.visible( false );
                        }
                    } else {
                        // system error
                        item.stopDeletionCountdown();
                        alert( response.data );
                    }

                },
                error: function( response ) {
                    console.log( response );
                }
            } );

            Types.RepeatableGroup.Functions.cssExtension();
        };

        /**
         * Add item to group
         * @param field
         */
        self.addItem = function( field, event ) {
            var parentPostId =  typeof field.item !== 'undefined'
                ? field.item.id
                : self.parent_post_id;

            // ajax call to create new item... return will be the form inputs
            $.ajax( {
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: ajaxInfo.action.name,
                    wpnonce: ajaxInfo.action.nonce,
                    parent_post_id: parentPostId,
                    repeatable_group_action: 'json_repeatable_group_add_item',
                    repeatable_group_id: self.id
                },
                dataType: 'json',
                success: function( response ) {
                    if( response.success ) {
                        if( self.items().length == 0 ) {
                            self.toggleGroupVisibility();
                        }

                        var newItem = new Types.RepeatableGroup.Model.Item( response.data.item, self );
                        self.items.push( newItem );
                        newItem.toggleVisibility();
                        newItem.title( '' );
                        newItem.editTitleStart();
                        Types.RepeatableGroup.Functions.cssExtension();

                        // legacy control of file fields must be initialized again
                        Types.RepeatableGroup.Functions.initLegacyFields();

                        // trigger WYSIWYG reInit
                        jQuery( document ).trigger( 'toolset:types:reInitWYSIWYG', response.data.item);

                        // set field conditions for new item
                        Types.RepeatableGroup.Functions.setFieldConditions( response.data.fieldConditions );

                    }
                },

                error: function( response ) {
                    console.log( response );
                }
            } );

            return;
        }

        self.listHeadlines = function() {
            var headlinesList = '';
            ko.utils.arrayForEach( this.headlines(), function( headline ) {
                headlinesList += headline.title + '<br />';
            } );
            return headlinesList;
        }

        // Disable Item Title Introduction
        self.itemTitleIntroductionActive = ko.observable( data.itemTitleIntroductionActive );

        self.disableTitleIntroduction = function( item ) {
            self.itemTitleIntroductionActive( false );
            item.editTitleStart();

            // store decision to usermeta
            $.ajax( {
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: ajaxInfo.action.name,
                    wpnonce: ajaxInfo.action.nonce,
                    repeatable_group_action: 'json_repeatable_group_item_title_introduction_dismiss',
                },
                dataType: 'json',
                success: function( response ) {},
                error: function( response ) {}
            } );
        }


         /**
         * Hook on WPToolset_Form_Conditional script toggle event to determine if a rfg field should be shown or not
         */
        jQuery( document ).on( 'js_event_toolset_forms_conditional_field_toggled', function( e, data ) {
            var fieldName = data.container.attr( 'name' );
            if( ! fieldName || ! fieldName.startsWith( "types-repeatable-group" ) ) {
                // no rfg item field
                return;
            }

            // get id and field slug
            var explodeName = fieldName.match(/\[(.*?)\]\[(.*?)\]/);

            var itemId = explodeName[1],
                fieldSlug = explodeName[2];

            // find affected item by item id
            var affectedItem = ko.utils.arrayFirst( self.items(), function( item ) {
                return item.id == itemId;
            });

            if( ! affectedItem ) {
                // no item found
                return;
            }

            // find affected field by field slug
            var affectedField = ko.utils.arrayFirst( affectedItem.fields(), function( field ) {
                return field.metaKey == fieldSlug || field.metaKey == 'wpcf-' + fieldSlug;
            });

            if( ! affectedField ) {
                // no field found
                return;
            }

            // set visibility
            affectedField.fieldConditionsMet( data.visible ? true : false );
        } );
    }

    /**
     * Headline model
     *
     * @param data
     * @constructor
     */
    Types.RepeatableGroup.Model.Headline = function( data ) {
        this.title = data.title || '';
        this.wpmlIsCopied = data.wpmlIsCopied || 0;
    }

    /**
     * Item model
     *
     * @param data
     * @param group
     * @constructor
     */
    Types.RepeatableGroup.Model.Item = function( data, group ) {
        var self = this;
        self.id = data.id || 0;
        self.title = ko.observable( data.title || '' );
        self.titleBeforeChange = self.title();
        self.group = group;
        self.activeNestedGroups = ko.observable( 0 );
        self.currentNestedActiveGroup = null;
        self.secondsToDelete = 4;
        self.shouldBeDeleted = ko.observable( false );
        self.shouldBeDeletedSeconds = ko.observable( self.secondsToDelete );
        self.shouldBeDeletedCountdown = null;
        self.visible = ko.observable( false );
        self.summaryString = ko.observable( data.id );
        self.fields = ko.observableArray( ko.utils.arrayMap( data.fields || [],
            function( fieldData ) {
                return new Types.RepeatableGroup.Model.Field( fieldData, self );
            } )
        );

        /*
         * Edit Item Title
         */
        self.editTitleTriggerVisible = ko.observable( false );
        self.editTitleTriggerShow = function() { self.editTitleTriggerVisible( true ); };
        self.editTitleTriggerHide = function() { self.editTitleTriggerVisible( false ); };

        self.editTitleActive = ko.observable( false );

        // Callback when the user starts editing the tilte
        self.editTitleStart = function( index ) {
            self.editTitleActive( true );

            // this will select all text of the title input
            $( '.js-rg-title-input:focus' ).select();

            // allow to use "tabulator" to go to next rfg item title
            $( window ).on( 'keydown.types-rfg-change-title-'+self.id, function( e ){
                if( e.keyCode == 9 ) { // tab
                    if( self.group.items()[index] && ! self.group.items()[index].visible() ) {
                        // current item fields are not visible. In this case "Tab" will go to next item title
                        var nextItemIndex = ( self.group.items()[index+1] ) ? index + 1 : 0;
                        self.group.items()[nextItemIndex].editTitleStart(nextItemIndex);
                        return false;
                    }
                }
            });
            // allow to use "enter" to save and end the title editing
            // (without this "enter" would trigger post update and reload the page)
            $( window ).on( 'keydown.types-rfg-save-title-'+self.id, function( e ){
                if( e.keyCode == 13 ) { // enter
                    $( '.js-rg-title-input:focus' ).blur(); // unfocus input
                    return false;
                }
            });
        };

        // Callback when the user finished editing the title (triggered on blur() of title input)
        self.editTitleDone = function() {
           // edit done = remove "tabulator" & "enter" event
            $( window ).off( 'keydown.types-rfg-change-title-'+self.id );
            $( window ).off( 'keydown.types-rfg-save-title-'+self.id );

            // save title
            if( self.title() != self.titleBeforeChange ) {
                self.titleBeforeChange = self.title();

                $.ajax( {
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: ajaxInfo.action.name,
                        wpnonce: ajaxInfo.action.nonce,
                        repeatable_group_action: 'json_repeatable_group_item_title_update',
                        item_id: self.id,
                        item_title: self.title()
                    },
                    dataType: 'json',
                    success: function( response ) {
                        if( ! response.success ) {
                            // Technical issue
                            alert( response.data );
                        }
                    },
                    error: function( response ) {}
                } );
            }
        };

        // we need a special transition for the introduction tooltip, it can't be just
        ko.bindingHandlers.typesRFGTitleIntroductionVisible = {
            init: function( el, observable ) {
                // trigger update callback on init
                $( el ).toggle( observable() );
            },
            update: function( el, observable ) {
                // fade in / out and display block/none afterwards to make sure it's clickable or not
                var $el = $( el );
                observable()
                    ? $el.fadeIn( 120, function() { $el.show() } )
                    : $el.fadeOut( 120, function() { $el.hide() } );
            }
        };

        /*
         * Start Deletion
         */
        self.startDeletionCountdown = function() {
            self.shouldBeDeleted( true );

            self.shouldBeDeletedCountdown = setInterval( function() {
                if( self.shouldBeDeletedSeconds() === 0 ) {
                    clearInterval( self.shouldBeDeletedCountdown );
                    self.group.removeItem( self );
                } else {
                    self.shouldBeDeletedSeconds( self.shouldBeDeletedSeconds() - 1 );
                }
            }, 1000 );
        }

        /*
         * Stop Deletion
         */
        self.stopDeletionCountdown = function() {
            self.shouldBeDeleted( false );
            self.shouldBeDeletedSeconds( self.secondsToDelete );
            clearInterval( self.shouldBeDeletedCountdown );
        }

        /*
         * We need to store the element, because otherwise the element will be re-applied
         * to the view model, which would mean a lose of all user changes (input fields).
         */
        self.storeItemForSortable = function( el ) {
            ko.utils.domData.set( el, 'originalItem', self );
        }

        self.updateFields = function() {
            ko.utils.arrayForEach( this.fields(), function( field ) {
                if( field.repeatableGroup !== null ) {
                    ko.utils.arrayForEach( field.repeatableGroup.items(), function( item ) {
                        item.updateFields();
                    } );
                }

                field.typesUpdateHtmlInput();
            } );
        }

        /**
         * Toggle visibility
         */
        self.toggleVisibility = function() {
            /* Allow only one open item
            ko.utils.arrayForEach( self.group.items(), function( item ) {
                if( item.visible ) {
                    item.visible( false );
                }
            } );
            */

            self.visible( ! self.visible() );
            Types.RepeatableGroup.Functions.initLegacyFields();
            Types.RepeatableGroup.Functions.cssExtension();

	        // Fire 'toolset_types_rfg_item_toggle' event when the item is toggled
	        $( document ).trigger( 'toolset_types_rfg_item_toggle', [ self ] );

	        if( self.visible() ) {
                // disable summary on open
                self.summaryString( '' );

            } else {
                // update summary if closed
                ko.utils.arrayForEach( this.fields(), function( field ) {
                    if( field.repeatableGroup === null ) {
                        field.typesUpdateHtmlInput();
                    }
                } );

                self.updateSummary();
            }

            // Have to remove these classes because if don't it is not uploaded.
            jQuery( '[data-item-id=' + self.id + '] .js-wpt-remove-on-submit' ).removeClass( 'js-wpt-remove-on-submit' );
        }


        self.updateSummary = function() {
            var newSummaryString = '';
            ko.utils.arrayForEach( this.fields(), function( field ) {
                if( field.repeatableGroup === null ) {
                    field.updateUserValue();
                    if( field.userValue != '' ) {
                        newSummaryString = newSummaryString + field.userValue + ', ';
                    }
                }
            } );

            newSummaryString = newSummaryString.replace( /,\s*$/, '' );
            newSummaryString = newSummaryString.slice(0, 100) + ( newSummaryString.length > 100 ? '...' : '' );

            self.summaryString( newSummaryString );
        }

        self.updateSummary();
    }

    /**
     * Field model
     *
     * @param data
     * @param item
     * @constructor
     */
    Types.RepeatableGroup.Model.Field = function( data, item ) {
        var self = this;
        self.item = item;
        self.title = data.title || '';
        self.metaKey = data.metaKey || '';
        self.wpmlIsCopied = data.wpmlIsCopied || 0;
        self.htmlInput = data.htmlInput || '';
        self.element = '';
        self.userValue = data.value || '';
        self.fieldConditionsMet =
            $( self.htmlInput ).filter( '.js-toolset-conditional' ).length  // class for fields with conditions
            && $( self.htmlInput ).filter( '.wpt-hidden' ).length           // legacy class when the field is hidden
            ? ko.observable( false )
            : ko.observable( true );

        self.repeatableGroup = ( "repeatableGroup" in data )
            ? new Types.RepeatableGroup.Model.Group( data.repeatableGroup, self.item.group.level + 1, self )
            : null;

        self.setElement = function( el ) {
            self.element = el;
        }

        self.typesUpdateHtmlInput = function() {
            self.htmlInput = $( self.element ).typesUpdateHtml();
        }

        self.updateUserValue = function() {
            var newValue = '';

            $( 'input[type="checkbox"][name^="types-repeatable-group"]:checked, input[type="radio"][name^="types-repeatable-group"]:checked', self.htmlInput ).each( function() {
                var inputLabel = $( this ).parent().find( 'label' ).html();
                inputLabel = inputLabel.slice(0, 25 ) + ( inputLabel.length > 25 ? '...' : '' );
                newValue = newValue + inputLabel + ', ';
            } );

            $( 'input[type="text"], textarea', self.htmlInput ).each( function() {
                if( this.value != '' ) {
                    newValue = this.value.slice(0, 25 ) + ( this.value.length > 25 ? '...' : '' );
                }
            } );

            self.userValue = newValue.replace( /,\s*$/, '' );
        }

        /**
         * Get original translation data
         * @param data
         */
        self.getOriginalTranslation = function( field, trigger ) {
           var $el = $( trigger.target );

           // remove hover tooltip
           $el.trigger( 'mouseout' );
           $el.removeClass( 'js-wpcf-tooltip' );

           // tooltip which shows original language
           var $tooltip = $el.next();

           if( $tooltip.data( 'translation-loaded' ) == 0 ) {
               $.ajax( {
                   url: ajaxurl,
                   type: 'POST',
                   data: {
                       action: ajaxInfo.action.name,
                       wpnonce: ajaxInfo.action.nonce,
                       repeatable_group_action: 'json_repeatable_group_field_original_translation',
                       repeatable_group_id: self.item.id,
                       field_meta_key: self.metaKey
                   },
                   dataType: 'json',
                   success: function( response ) {
                       $tooltip.data( 'translation-loaded', 1 );
                       $tooltip.html( response.data );
                   },
                   error: function( response ) {
                       console.log( response );
                   }
               } );
           }

            $tooltip.toggle();
            $el.toggleClass( 'field-translation-trigger-active' );
        };
    }

    /**
     * Collection of generic functions
     */
    Types.RepeatableGroup.Functions = {
        /*
         * cssExtension
         */
        'cssExtension': function() {
            var rgx = $( '.js-rgx' ),
                rgy = $( '.js-rgy' );

            // task for horizontal view
            if( rgx.length ) {

                // position the fixed columns (sort handle and delete of first item level)
                rgx.find( 'th:last-child' ).css( 'left', ( $( '.c-rgx__body' ).offset().left + $( '.c-rgx__body' ).width()  ) + 'px' );
                rgx.find( '.c-rgx__level--1' ).each( function() {
                    var tbodyHeight = $( this ).closest( 'tbody' ).outerHeight() + 1;

                    $( this ).css( 'height', tbodyHeight + 'px' );
                    $( this ).find( 'i.fa' ).each( function() {
                        $( this ).css( {
                            'position' : 'absolute',
                            'top': ( tbodyHeight - $( this ).height() ) / 2,
                            'left': '20%'
                        } );
                    } );

                    $( this ).css( 'top', $( this ).closest( 'tbody' ).offset().top - $( window ).scrollTop() + 'px' );
                } );

                // adjust the size of the container with the delete countdown
                rgx.find( '.js-rg-countdown' ).each( function() {
                    $( this ).parent().css( 'position', 'relative' );
                    var parentTr = $( this ).closest( 'tbody' );
                    var isNested = parentTr.closest( '.js-rgx__td--group-container' );
                    var width = parentTr.get( 0 ).clientWidth;

                    if( isNested.length > 0 ) {
                        width = 0;
                        $( this ).closest( 'tr' ).find( 'td' ).each( function() {
                            width += $( this ).get( 0 ).clientWidth;
                        } );
                    }

                    $( this ).css( {
                        'width': width + 'px',
                        'line-height': parentTr.get( 0 ).clientHeight + 'px',
                        'height': parentTr.get( 0 ).clientHeight + 'px'
                    } );
                } );
            }

            // task for vertical view
            if( rgy.length ) {

                // adjust the size of the container with the delete countdown
                rgy.find( '.js-rg-countdown' ).each( function() {
                    var parent = $( this ).parent();
                    parent.css( 'position', 'relative' );

                    var width = parent.get( 0 ).clientWidth,
                        height= parent.get( 0 ).clientHeight;

                    $( this ).css( {
                        'width': width + 'px',
                        'line-height': height + 'px',
                        'height': height + 'px'
                    } );
                } );
            }

        },

        /*
         * Make legacy fields work
         */
        'initLegacyFields': function() {
            if( typeof wptDate != 'undefined' ) {
                wptDate.init('body');
            }

            if( typeof wptColorpicker != 'undefined' ) {
                wptColorpicker.init('body');
            }

            if( typeof wptValidation != 'undefined' ) {
                wptValidation.init();
            }

            if( typeof wptSkype != 'undefined' ) {
                wptSkype.init();
            }

            if( typeof wptFile != 'undefined' ) {
                wptFile.init();
            }
        },

        /**
         * Set Conditions
         */
        'setFieldConditions': function( conditions ) {
            if( conditions && wptCond) {

                // wptCond.addConditionals( conditions ) fails when the triggers/fields for formId are undefined
                // better fixing it here to prevent any side effects (for which this behaviour might be necessary)
                _.each( conditions, function ( condition, formID) {
                    if ( _.size( condition.triggers ) && typeof wptCondTriggers[formID] == 'undefined' ) {
                        wptCondTriggers[formID] = {};
                    }
                    if ( _.size( condition.fields )  && typeof wptCondFields[formID] == 'undefined' ) {
                        wptCondFields[formID] = {};
                    }
                    if ( _.size( condition.custom_triggers ) && typeof wptCondCustomTriggers[formID] == 'undefined' ) {
                        wptCondCustomTriggers[formID] = {};
                    }
                    if ( _.size( condition.custom_fields ) && typeof wptCondCustomFields[formID] == 'undefined' ) {
                        wptCondCustomFields[formID] = {};
                    }
                } );

                // add conditionals
                wptCond.addConditionals( conditions );

                // check show/hide for all conditions, this is important for rfg field conditions,
                // which use a field outside of the rfg for the condition.
                $.each( wptCondTriggers, function( formID, triggers ) {
                    $.each( triggers, function( trigger, field ) {
                        var $trigger = wptCond.getTrigger( trigger, formID );
                        $trigger.trigger( 'change' );
                    } )
                } );
            }
        },

        /**
         * Scan all fields and return only textareas with wpt-wysiwyg class
         * @param items
         * @returns {Array}
         */
        'getTinyMCEIds' : function ( items ) {

            var editors = [];

            $.each( items, function( groupItem, groupItemValue ) {
                $.each( groupItemValue.fields, function( singleGroupFields, singleGroupFieldsValues ) {
                    var fieldObject = jQuery( singleGroupFieldsValues.htmlInput );
                    if( jQuery( 'textarea', fieldObject ).hasClass( 'wpt-wysiwyg' ) ){
                        var editorID = jQuery( 'textarea', fieldObject ).attr( 'id' );
                        editors.push( editorID );
                    }
                } )
            } );

            return editors;
        }


    }

    /**
     * Mapper for the autogenerated (using ko.mapping) viewModel
     */
    Types.RepeatableGroup.Mapper = {
        'repeatableGroup': {
            create: function( options ) {
                return new Types.RepeatableGroup.Model.Group( options.data, 0 );
            }
        }
    }

    /**
     * Sortable Items
     */
    ko.bindingHandlers.typesRepeatableGroupSortable = {
        init: function( el, valueAccessor, allBindingsAccesor, context ) {
            var element = $( el ),
                list = valueAccessor(),
                sortableHandle = '.c-rgx_sort--handle',
                sortableContainer = $( '.c-rgx__body' );

            if( list().length ) {
                if( list()[0].group.controlsActive == 0 ) {
                    return;
                }
            }
            element.sortable( {
                axis: 'y',
                scroll: true,
                handle: sortableHandle,
                tolerance: 'pointer',
                cancel: ".c-rgx__sort--item-disabled",
                scroll: false,
                forcePlaceholderSize: true,
                start: function( e, ui ) {
                    // size the placeholder propably
                    ui.placeholder.find( 'tr' ).height( ui.helper.outerHeight() );
                    ui.placeholder.css( 'visibility', 'inherit' );
                    ui.placeholder.find( 'tr:first-child' ).children( 'td' ).replaceWith( function( i, html ) {
                        return '<th class="c-rgx__th" style="opacity: 1;">' + html + '</th>';
                    } );

                    var el = ui.item[ 0 ];
                    ko.utils.domData.set( el, 'originalIndex', ko.utils.arrayIndexOf( ui.item.parent().children(), el ) - 1 );

                    // make sure placeholder has same width as helper
                    var helperCells = ui.helper.find( 'tr:first-child' ).children();
                    ui.placeholder.find( 'tr:first-child' ).children().each( function( index ) {
                        $( this ).width( helperCells.eq( index ).width() );
                    } );

                    // hide overflow otherwise scrollbars could be produced by dragging the dragged item outside the box
                    sortableContainer.css( 'overflow-y', 'hidden' );
                },
                sort: function() {
                    Types.RepeatableGroup.Functions.cssExtension();
                },
                stop: function() {
                    // reset overflow
                    sortableContainer.css( 'overflow-y', 'visible' );
                    Types.RepeatableGroup.Functions.cssExtension();
                },
                helper: function( e, tbody ) {
                    // 1:1 copy for the helper
                    var originalCells = tbody.find( 'tr:first-child' ).children(),
                        helper = tbody.clone();

                    // make sure helper has same width as original
                    helper.find( 'tr:first-child' ).children().each( function( index ) {
                        $( this ).width( originalCells.eq( index ).width() );
                    } );

                    return helper;
                },
                update: function( event, ui ) {
                    // this whole update function resorts the elements in the knockout observable array
                    // the nasty part here is that this must happen without reinitialise the element
                    // (otherwise any user changes to the input would be resetted)
                    var el = ui.item[ 0 ];
                    var item = ko.utils.domData.get( el, 'originalItem' ),
                        newIndex = ko.utils.arrayIndexOf( ui.item.parent().children(), ui.item[ 0 ] ) - 1;

                    item.updateFields();

                    if( newIndex >= list().length ) newIndex = list().length - 1;
                    if( newIndex < 0 ) newIndex = 0;

                    ui.item.remove();

                    list.splice( ko.utils.domData.get( el, 'originalIndex' ), 1 );
                    list.splice( newIndex, 0, item );
                }
            } );
        }
    };



    /**
     * @var null|boolean
     */
    self.isWpEditorAvailable = null;


    /**
     * Check whether wp.editor is available
     *
     * @return {bool}
     */
    self.checkWpEditorAvailable = function() {
        if ( null == self.isWpEditorAvailable ) {
            self.isWpEditorAvailable = (
                _.has( window, 'wp' )
                && _.has( window.wp, 'editor' )
                && _.has( window.wp.editor, 'remove' )
                && _.has( window.wp.editor, 'initialize' )
            );
        }
        return self.isWpEditorAvailable;
    };



    /**
     * TODO: Since we are using this code on the two places, it would be good to move it in common maybe
     *
     * Initialize WYSIWYG editors on demand
     *
     * If wp.editor is available (set by the textarea classname flag) use it to initialize the field;
     * otherwise, show just a textarea.
     *
     * @param {string} The underlying textarea id attribute.
     * @fires event:toolset:types:wysiwygFieldInited
     */
    self.initWysiwygField = function( id ) {
        if ( self.checkWpEditorAvailable() ) {
            // WordPress over 4.8, hence wp.editor is available and included
            wp.editor.remove( id );
            wp.editor.initialize( id, { tinymce: true, quicktags: true, mediaButtons: true } );
            jQuery( '#wp-' + id + '-wrap .wp-media-buttons' ).attr( 'id', 'wp-' + id + '-media-buttons' );
            /**
             * Broadcasts that the WYSIWYG field initialization was completed
             *
             * @param {string} The underlying textarea id attribute
             *
             * @event toolset:types:wysiwygFieldInited
             */
            jQuery( document ).trigger( 'toolset:types:wysiwygFieldInited', [ id ] );

        } else {
            // WordPress below 4.8, hence wp-editor is not available
            // so we turn those fields into simple textareas
            jQuery( '#wp-' + id + '-editor-tools' ).remove();
            jQuery( '#wp-' + id + '-editor-container' )
                .removeClass( 'wp-editor-container' )
                .find( '.mce-container' )
                .remove();
            jQuery( '#qt_' + id + '_toolbar' ).remove();
            jQuery( '#' + id )
                .removeClass( 'wp-editor-area' )
                .show()
                .css( { width: '100%' } );
        }
    };

    $( document ).on( 'toolset:types:reInitWYSIWYG', function( event, fieldItem ) {
        var tinyMCEEditors = Types.RepeatableGroup.Functions.getTinyMCEIds( [fieldItem] );

        if(tinyMCEEditors.length === 0){
            return;
        }

        $.each( tinyMCEEditors, function( editor, editorValue ) {
            self.initWysiwygField( editorValue );
        });
    });

    /**
     * Initialize the groups
     * We're loading the groups after page load via Ajax
     */
    $( document ).on( 'ready', function() {
        var positioningInit = false;
        var repeatableGroups = $( 'div[data-types-repeatable-group]' );

        if( repeatableGroups.length ) {
            var tplRepeatableGroup = $( '#tplRepeatableGroup' ).html();

            // load all items of all groups
            repeatableGroups.each( function() {
                var repeatableGroup = $( this );

                if( ajaxInfo.post_id == 0 ) {
                    repeatableGroup.find( '.js-rgx__notice_loading' ).hide();
                    repeatableGroup.find( '.js-rgx__notice_save_post_first' ).show();
                } else {
                    $.ajax( {
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: ajaxInfo.action.name,
                            wpnonce: ajaxInfo.action.nonce,
                            parent_post_id: ajaxInfo.post_id,
                            repeatable_group_action: 'json_repeatable_group',
                            repeatable_group_id: repeatableGroup.data( 'types-repeatable-group' )
                        },
                        dataType: 'json',
                        success: function( response ) {
                            if( response.success ) {
                                repeatableGroup.html( tplRepeatableGroup );
                                ko.applyBindings( ko.mapping.fromJS( response.data, Types.RepeatableGroup.Mapper ), repeatableGroup.get( 0 ) );
                                Types.RepeatableGroup.Functions.cssExtension();
                                if( positioningInit === false ) {
                                    positioningInit = true;
                                    // we need this on resize and scroll to make sure the fixed positioned columns are always correctly positioned
                                    $( window ).on( 'resize scroll', Types.RepeatableGroup.Functions.cssExtension );
                                }
                                Types.RepeatableGroup.Functions.initLegacyFields();

                                // Get WYSIWYG Fields and reinitialize tinyMCE editors
                                var tinyMCEEditors = Types.RepeatableGroup.Functions.getTinyMCEIds( response.data.repeatableGroup.items );
                                $.each( tinyMCEEditors, function( editor, editorValue ) {
                                    self.initWysiwygField( editorValue );
                                });

                                // run field validation after fields are loaded
                                jQuery( document ).trigger( 'toolset_ajax_fields_loaded', [{form_id: 'post'}] );

                                // set field conditions for rfg items
                                Types.RepeatableGroup.Functions.setFieldConditions( response.data.repeatableGroup.fieldConditions );
                            } else {
                                // todo proper response if rfg couldn't be loaded
                                console.log( 'Repeatable Field Group with ID "' + repeatableGroup.data( 'types-repeatable-group' ) + '" could not be loaded.' );
                            }
                        },

                        error: function( response ) {
                            // todo proper response
                            console.log( response );
                        }
                    } );
                }
            } );
        }

        // Check conditionals after adding items or initialy
        Toolset.hooks.addAction( 'toolset-conditionals-add-conditionals', function( id ) {
            Object.keys( arguments[0]['#post'].fields ).forEach( function( groupId ) {
                var id = groupId.replace( /^.*\[(\d+)\].*$/, '$1' );
                jQuery( '[data-item-id=' + id + '] [name]' ).each( function() {
                    var name = this.getAttribute( 'name' );
                    if ( !!wptCondFields['#post'][ name ] ) {
                        wptCond.check( '#post', [ name ] );
                    }
                } );
            } );
        } );
    } );

    // block vertical / horizontal view switch when there was some change done
    $( document ).on( 'keydown.rfgBlockViewSwitch change.rfgBlockViewSwitch', ':input', function() {
        // deregister event (no need to run twice)
        $( document ).off( 'keydown.rfgBlockViewSwitch change.rfgBlockViewSwitch' );

        // disable button
        $( '.js-rfg-view-switch' ).addClass( 'js-wpcf-tooltip js-rfg-view-switch-disabled' );

        // disable link of button
        $( '.js-rfg-view-switch-disabled' ).on( 'click', function( e ) {
            e.preventDefault();
        } );
    });

    // Saving draft button when post is not saved yet
    jQuery( document ).on( 'click', '#wpcf-save-post', function() {
        // Save post button.
        jQuery( '#save-post').click();
        // Disables and show spinner.
        jQuery( this ).attr('disabled', 'disabled').next().addClass( 'is-active' );
    } );
})( jQuery );
