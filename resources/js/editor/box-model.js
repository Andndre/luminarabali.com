export default function EditorBoxModel() {
    return {
        updateBoxState(type, updates) {
                if (!this.selectedNode) return;

                let state = {};
                const sides = (type === 'rounded') ? ['tl', 'tr', 'bl', 'br'] : ['t', 'b', 'l', 'r'];
                sides.forEach(s => { 
                    const val = this.getBoxValue(type, s);
                    if (val && val !== '') state[s] = val;
                });

                Object.keys(updates).forEach(k => {
                    if (updates[k] === null || updates[k] === '') delete state[k];
                    else state[k] = updates[k];
                });

                let newClasses = [];

                const buildClass = (prefix, val) => {
                    if (val === 'default') return prefix;
                    const strVal = String(val);
                    const isNeg = strVal.startsWith('-');
                    const absVal = isNeg ? strVal.substring(1) : strVal;
                    return (isNeg ? '-' : '') + prefix + '-' + absVal;
                };

                if (type === 'p' || type === 'm' || type === 'border') {
                    const prefix = (type === 'border') ? 'border' : type;
                    const sep = (type === 'border') ? '-' : '';
                    
                    if (state.t && state.t === state.b && state.t === state.l && state.t === state.r) {
                        newClasses.push(buildClass(prefix, state.t));
                    } else {
                        if (state.t && state.t === state.b) {
                            newClasses.push(buildClass(prefix + sep + 'y', state.t));
                            delete state.t; delete state.b;
                        }
                        if (state.l && state.l === state.r) {
                            newClasses.push(buildClass(prefix + sep + 'x', state.l));
                            delete state.l; delete state.r;
                        }
                        if (state.t) newClasses.push(buildClass(prefix + sep + 't', state.t));
                        if (state.b) newClasses.push(buildClass(prefix + sep + 'b', state.b));
                        if (state.l) newClasses.push(buildClass(prefix + sep + 'l', state.l));
                        if (state.r) newClasses.push(buildClass(prefix + sep + 'r', state.r));
                    }
                } else if (type === 'rounded') {
                    if (state.tl && state.tl === state.tr && state.tl === state.bl && state.tl === state.br) {
                        newClasses.push(buildClass('rounded', state.tl));
                    } else {
                        if (state.tl && state.tl === state.tr) {
                            newClasses.push(buildClass('rounded-t', state.tl));
                            delete state.tl; delete state.tr;
                        } else if (state.bl && state.bl === state.br) {
                            newClasses.push(buildClass('rounded-b', state.bl));
                            delete state.bl; delete state.br;
                        }
                        if (state.tl && state.tl === state.bl) {
                            newClasses.push(buildClass('rounded-l', state.tl));
                            delete state.tl; delete state.bl;
                        } else if (state.tr && state.tr === state.br) {
                            newClasses.push(buildClass('rounded-r', state.tr));
                            delete state.tr; delete state.br;
                        }
                        if (state.tl) newClasses.push(buildClass('rounded-tl', state.tl));
                        if (state.tr) newClasses.push(buildClass('rounded-tr', state.tr));
                        if (state.bl) newClasses.push(buildClass('rounded-bl', state.bl));
                        if (state.br) newClasses.push(buildClass('rounded-br', state.br));
                    }
                }

                let classes = (this.nodeData.classes || '').split(' ').filter(c => c.trim() !== '');
                classes = classes.filter(c => {
                    const cleanC = c.startsWith('-') ? c.substring(1) : c;
                    const cleanBase = cleanC.split('-')[0];
                    
                    if (type === 'rounded') {
                        return !cleanC.startsWith('rounded');
                    } else if (type === 'p' || type === 'm' || type === 'border') {
                        return cleanBase !== type && !cleanC.startsWith(type + '-');
                    }
                    return true;
                });

                classes.push(...newClasses);
                this.nodeData.classes = classes.join(' ');
                this.updateNodeProperty('classes', this.nodeData.classes);
            },

            setBoxValue(type, side, val, event = null) {
                if (!val || val.trim() === '') {
                    val = null;
                } else {
                    val = val.trim();
                    let isNegative = false;

                    if (val.startsWith('-')) {
                        isNegative = true;
                        val = val.substring(1);
                    }

                    if (!val.includes('[')) {
                        if (/^\d+(\.\d+)?$/.test(val) || ['auto', 'none', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', 'full'].includes(val)) {
                            // keep as is
                        } else if (val !== 'default') {
                            val = '[' + val + ']';
                        }
                    }
                    
                    if (isNegative && val !== 'default') {
                        val = '-' + val;
                    }
                }

                let updates = {};
                let isGlobal = this.constraints[type + 'Global'];
                let isAlt = event && event.altKey;
                const sides = (type === 'rounded') ? ['tl', 'tr', 'bl', 'br'] : ['t', 'b', 'l', 'r'];

                if (isGlobal) {
                    sides.forEach(s => updates[s] = val);
                } else if (isAlt) {
                    if (type === 'rounded') {
                        if (side === 'tl' || side === 'tr') { updates.tl = val; updates.tr = val; }
                        else if (side === 'bl' || side === 'br') { updates.bl = val; updates.br = val; }
                    } else {
                        if (side === 't' || side === 'b') { updates.t = val; updates.b = val; }
                        else if (side === 'l' || side === 'r') { updates.l = val; updates.r = val; }
                    }
                } else {
                    updates[side] = val;
                }

                this.updateBoxState(type, updates);
            },

            getBoxValue(type, side) {
                const classes = (this.nodeData.classes || '').split(' ');

                const extractValue = (prefix) => {
                    const regex = new RegExp('^-?' + prefix + '(?:-|$)');
                    const match = classes.find(c => regex.test(c));
                    if (!match) return null;
                    
                    const isNegative = match.startsWith('-');
                    const replaceRegex = new RegExp('^-?' + prefix + '-?');
                    let val = match.replace(replaceRegex, '');
                    
                    if (val === '') val = 'default';
                    return (isNegative ? '-' : '') + val;
                };

                if (type === 'p' || type === 'm') {
                    const axis = (side === 't' || side === 'b') ? 'y' : 'x';
                    let val = extractValue(type + side);
                    if (val !== null) return val;
                    
                    val = extractValue(type + axis);
                    if (val !== null) return val;
                    
                    val = extractValue(type);
                    return val !== null ? val : '';
                }

                if (type === 'border') {
                    const axis = (side === 't' || side === 'b') ? 'y' : 'x';
                    let val = extractValue('border-' + side);
                    if (val !== null) return val;
                    
                    val = extractValue('border-' + axis);
                    if (val !== null) return val;
                    
                    val = extractValue('border');
                    return val !== null ? val : '';
                }

                if (type === 'rounded') {
                    let val = extractValue('rounded-' + side);
                    if (val !== null) return val;
                    
                    let edge1 = null, edge2 = null;
                    if (side === 'tl') { edge1 = 't'; edge2 = 'l'; }
                    else if (side === 'tr') { edge1 = 't'; edge2 = 'r'; }
                    else if (side === 'bl') { edge1 = 'b'; edge2 = 'l'; }
                    else if (side === 'br') { edge1 = 'b'; edge2 = 'r'; }

                    if (edge1 !== null) {
                        val = extractValue('rounded-' + edge1);
                        if (val !== null) return val;
                    }
                    if (edge2 !== null) {
                        val = extractValue('rounded-' + edge2);
                        if (val !== null) return val;
                    }
                    
                    val = extractValue('rounded');
                    return val !== null ? val : '';
                }

                return '';
            }
    };
}
