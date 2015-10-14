Handlebars.registerHelper('eachkeys', function(context, options) {
    var fn = options.fn, inverse = options.inverse;
    var ret = "";

    var empty = true;
    for (key in context) { empty = false; break; }

    var index = 0;

    if (!empty) {
        for (key in context) {
            ret = ret + fn({ 'key': key, 'value': context[key], 'index': index });
            index = index + 1;
        }
    } else {
        ret = inverse(this);
    }
    return ret;
});

Handlebars.registerHelper('eachparse', function(context, options) {
    var fn = options.fn, inverse = options.inverse;
    var ret = "";
    var types = options.hash.types;

    var empty = true;

    for (key in context) { empty = false; break; }

    if ( ! empty) {
        var index = 0;
        for (key in context) {
            ret = ret + fn(parseValue(index, context[key], types));
            index = index + 1;
        }
    } else {
        ret = inverse(this);
    }
    return ret;
    
});

Handlebars.registerHelper('parseval', function(context, options) {

    var types = options.hash.types;
    var values = options.hash.values;

    return parseValue(context, values[context], types);
});

// parse table values
// @param index(int) - cell column index
// @param value (string) - value itself
function parseValue(index, value, values) {
    var type = values[index];
    switch(type) {
        case 'int':
            return parseInt(value, 10);
        case 'time':
            return (new Date).clearTime().addSeconds(parseInt(value,10)).toString('H:mm:ss');
        case 'string':
            return value;
        case 'percent':
            value = Math.round(value * 100) / 100;
            value = value.toFixed(2);
            return value + '%';
        case 'float':
            value = Math.round(value * 100) / 100;
            value = value.toFixed(2);
            return value;
    }
}

Handlebars.registerHelper('if_eq', function(context, options) {
    if (context == options.hash.compare)
        return options.fn(this);
    return options.inverse(this);
});

/**
 * Unless Equals
 * unless_eq this compare=that
 */
Handlebars.registerHelper('unless_eq', function(context, options) {
    if (context == options.hash.compare)
        return options.inverse(this);
    return options.fn(this);
});


/**
 * If Greater Than
 * if_gt this compare=that
 */
Handlebars.registerHelper('if_gt', function(context, options) {
    if (context > options.hash.compare)
        return options.fn(this);
    return options.inverse(this);
});

/**
 * Unless Greater Than
 * unless_gt this compare=that
 */
Handlebars.registerHelper('unless_gt', function(context, options) {
    if (context > options.hash.compare)
        return options.inverse(this);
    return options.fn(this);
});


/**
 * If Less Than
 * if_lt this compare=that
 */
Handlebars.registerHelper('if_lt', function(context, options) {
    if (context < options.hash.compare)
        return options.fn(this);
    return options.inverse(this);
});

/**
 * Unless Less Than
 * unless_lt this compare=that
 */
Handlebars.registerHelper('unless_lt', function(context, options) {
    if (context < options.hash.compare)
        return options.inverse(this);
    return options.fn(this);
});


/**
 * If Greater Than or Equal To
 * if_gteq this compare=that
 */
Handlebars.registerHelper('if_gteq', function(context, options) {
    if (context >= options.hash.compare)
        return options.fn(this);
    return options.inverse(this);
});

/**
 * Unless Greater Than or Equal To
 * unless_gteq this compare=that
 */
Handlebars.registerHelper('unless_gteq', function(context, options) {
    if (context >= options.hash.compare)
        return options.inverse(this);
    return options.fn(this);
});


/**
 * If Less Than or Equal To
 * if_lteq this compare=that
 */
Handlebars.registerHelper('if_lteq', function(context, options) {
    if (context <= options.hash.compare)
        return options.fn(this);
    return options.inverse(this);
});

/**
 * Unless Less Than or Equal To
 * unless_lteq this compare=that
 */
Handlebars.registerHelper('unless_lteq', function(context, options) {
    if (context <= options.hash.compare)
        return options.inverse(this);
    return options.fn(this);
});

/**
 * Convert new line (\n\r) to <br>
 * from http://phpjs.org/functions/nl2br:480
 */
Handlebars.registerHelper('nl2br', function(text) {
    var nl2br = (text + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + '<br>' + '$2');
    return new Handlebars.SafeString(nl2br);
});