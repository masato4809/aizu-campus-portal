import * as React from 'react';
import { useContext, useMemo } from 'react';
import { IPropsBase } from '@/script/System/System';
import { DialogLoading } from '@/script/Component/Misc/DialogLoading';
import { Closable } from '@/script/Component/Misc/Closable';

type ProgressContext = {
  progress: boolean;
  onStart: () => void;
  onFinish: () => void;
};
export const context = React.createContext<ProgressContext>({
  progress: false,

  onStart: () => {},

  onFinish: () => {},
});
export const useProgressContext = (): ProgressContext => useContext(context);

interface IProps extends IPropsBase {}
export const ProgressProvider: React.FC<IProps> = ({ children }) => {
  const [progress, setProgress] = React.useState<boolean>(false);

  /**
   * ロード開始時の処理.
   */
  const onStart = React.useCallback(() => {
    setProgress(true);
  }, []);

  /**
   * ロード終了時の処理.
   */
  const onFinish = React.useCallback(() => {
    setProgress(false);
  }, []);

  /**
   * 下位コンポーネントで利用するコントローラー経由のパラメータ.
   */
  const providerValue: ProgressContext = useMemo(
    () => ({
      progress,
      onStart,
      onFinish,
    }),
    [progress, onStart, onFinish],
  );

  return (
    <context.Provider value={providerValue}>
      <Closable open={providerValue.progress}>
        <DialogLoading loading={providerValue.progress} />
      </Closable>
      {children}
    </context.Provider>
  );
};
