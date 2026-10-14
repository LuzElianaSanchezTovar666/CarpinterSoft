import React, { useState } from 'react';
import { askCarpinterIA } from '../utils/aiAssistant';
import { 
  Bot, 
  X, 
  Send, 
  Sparkles, 
  HelpCircle, 
  CheckCircle2, 
  Hammer, 
  Compass, 
  Layers, 
  RefreshCw 
} from 'lucide-react';

interface AIAssistantModalProps {
  isOpen: boolean;
  onClose: () => void;
}

export const AIAssistantModal: React.FC<AIAssistantModalProps> = ({
  isOpen,
  onClose,
}) => {
  if (!isOpen) return null;

  const [question, setQuestion] = useState('');
  const [loading, setLoading] = useState(false);
  const [chatLog, setChatLog] = useState<
    { sender: 'user' | 'ai'; text: string; tips?: string[] }[]
  >([
    {
      sender: 'ai',
      text: `👋 ¡Hola Maestro! Soy **CarpinterIA**, tu asesor técnico en carpintería, cálculo de despiece, maderas (Roble, Cedro, Teca, Pino, Melaminas), calibración de herrajes y acabados profesionales. ¿En qué duda técnica de taller te puedo ayudar hoy?`,
      tips: [
        'Puedes preguntarme sobre holguras de correderas y bisagras.',
        'Puedo recomendarte acabados para maderas al exterior.',
      ],
    },
  ]);

  const handleAsk = async (textToAsk?: string) => {
    const q = textToAsk || question;
    if (!q.trim() || loading) return;

    const userMessage = { sender: 'user' as const, text: q };
    setChatLog((prev) => [...prev, userMessage]);
    setQuestion('');
    setLoading(true);

    try {
      const res = await askCarpinterIA(q);
      setChatLog((prev) => [
        ...prev,
        {
          sender: 'ai',
          text: res.answer,
          tips: res.tips,
        },
      ]);
    } catch (err) {
      setChatLog((prev) => [
        ...prev,
        {
          sender: 'ai',
          text: 'Ocurrió un inconveniente al consultar. Por favor intenta formular la pregunta nuevamente.',
        },
      ]);
    } finally {
      setLoading(false);
    }
  };

  const QUICK_QUESTIONS = [
    '¿Qué madera recomiendas para mesón de cocina resistente a humedad?',
    '¿Cómo calcular la holgura lateral para correderas telescópicas pesadas?',
    '¿Cómo descontar el espesor del tapacanto PVC 2mm en el despiece?',
    '¿Qué acabado es mejor para exterior: poliuretano catalizado o aceite?',
  ];

  return (
    <div className="fixed inset-0 z-50 bg-stone-950/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-6 overflow-y-auto">
      <div className="bg-stone-900 border border-stone-800 rounded-3xl max-w-2xl w-full max-h-[90vh] flex flex-col shadow-2xl animate-scaleUp">
        
        {/* Cabecera */}
        <div className="p-4 sm:p-5 border-b border-stone-800 flex items-center justify-between bg-stone-950/70 rounded-t-3xl">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-700 flex items-center justify-center text-stone-950 shadow-lg shadow-amber-900/40">
              <Bot className="w-5 h-5 text-stone-950" />
            </div>
            <div>
              <div className="flex items-center gap-2">
                <h3 className="text-base font-bold text-white font-['Outfit']">
                  Carpinter<span className="text-amber-400">IA</span> Asesor Técnico
                </h3>
                <span className="text-[10px] bg-amber-500/10 text-amber-400 font-bold px-2 py-0.5 rounded-full border border-amber-500/20">
                  Gemini Pro Taller
                </span>
              </div>
              <p className="text-xs text-stone-400">Consultas de ebanistería, maderas, maquinado y herrajes</p>
            </div>
          </div>

          <button
            onClick={onClose}
            className="p-1.5 rounded-xl bg-stone-800 hover:bg-stone-700 text-stone-400 hover:text-white transition-colors cursor-pointer"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        {/* Historial de Conversación */}
        <div className="p-4 sm:p-6 space-y-4 overflow-y-auto flex-1 text-xs">
          {chatLog.map((msg, index) => (
            <div
              key={index}
              className={`flex flex-col ${msg.sender === 'user' ? 'items-end' : 'items-start'}`}
            >
              <div
                className={`max-w-[85%] rounded-2xl p-4 leading-relaxed whitespace-pre-line ${
                  msg.sender === 'user'
                    ? 'bg-amber-600 text-stone-950 font-semibold rounded-tr-none'
                    : 'bg-stone-950 border border-stone-800 text-stone-200 rounded-tl-none space-y-2'
                }`}
              >
                <div>{msg.text}</div>

                {msg.tips && msg.tips.length > 0 && (
                  <div className="mt-3 pt-2.5 border-t border-stone-800 space-y-1">
                    <span className="text-[10px] font-bold text-amber-400 uppercase tracking-wider block">
                      💡 Consejos de Maestro:
                    </span>
                    {msg.tips.map((t, idx) => (
                      <div key={idx} className="flex items-start gap-1.5 text-[11px] text-stone-300">
                        <CheckCircle2 className="w-3.5 h-3.5 text-emerald-400 flex-shrink-0 mt-0.5" />
                        <span>{t}</span>
                      </div>
                    ))}
                  </div>
                )}
              </div>
            </div>
          ))}

          {loading && (
            <div className="flex items-center gap-2 text-stone-400 italic bg-stone-950/60 p-3 rounded-2xl border border-stone-800 w-fit">
              <RefreshCw className="w-3.5 h-3.5 animate-spin text-amber-400" />
              <span>Consultando manuales técnicos de carpintería...</span>
            </div>
          )}
        </div>

        {/* Sugerencias Rápidas */}
        <div className="p-3 bg-stone-950/60 border-t border-stone-800/80 overflow-x-auto no-scrollbar flex gap-2">
          {QUICK_QUESTIONS.map((q, idx) => (
            <button
              key={idx}
              onClick={() => handleAsk(q)}
              className="text-[11px] whitespace-nowrap px-3 py-1.5 rounded-xl bg-stone-800/80 hover:bg-stone-700 text-stone-300 hover:text-white border border-stone-700/60 transition-colors flex-shrink-0 cursor-pointer"
            >
              {q}
            </button>
          ))}
        </div>

        {/* Barra de Entrada de Pregunta */}
        <div className="p-4 border-t border-stone-800 bg-stone-950 rounded-b-3xl">
          <form
            onSubmit={(e) => {
              e.preventDefault();
              handleAsk();
            }}
            className="flex items-center gap-2"
          >
            <input
              type="text"
              placeholder="Escribe tu consulta técnica sobre ensambles, maderas, lacas o despiece..."
              value={question}
              onChange={(e) => setQuestion(e.target.value)}
              className="flex-1 px-4 py-2.5 bg-stone-900 border border-stone-800 rounded-xl text-xs text-white placeholder-stone-500 focus:outline-none focus:border-amber-500"
            />
            <button
              type="submit"
              disabled={loading || !question.trim()}
              className="p-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 disabled:opacity-40 text-stone-950 font-bold transition-all cursor-pointer"
            >
              <Send className="w-4 h-4" />
            </button>
          </form>
        </div>

      </div>
    </div>
  );
};
