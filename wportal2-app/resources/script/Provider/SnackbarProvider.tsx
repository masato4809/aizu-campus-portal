import * as React from 'react';
import { IPropsBase } from '@/script/System/System';
import { Snackbar } from '@mui/material';
import { Closable } from '@/script/Component/Misc/Closable';

const SnackbarContext = React.createContext<(text: string) => void>(
  () => undefined,
);
export const useSnackbar = () => React.useContext(SnackbarContext);

interface IProps extends IPropsBase {}
export const SnackbarProvider: React.FC<IProps> = ({ children }) => {
  const [message, setMessage] = React.useState<string>('');
  const displaySnackbar = React.useMemo(
    () => (text: string) => {
      setMessage(text);
    },
    [],
  );

  /**
   * スナックバーを表示するかどうか.
   */
  const isOpenSnackbar = (): boolean => {
    return !!message;
  };

  /**
   * スナックバー終了処理.
   */
  const handleCloseSnackbar = () => {
    setMessage('');
  };

  return (
    <SnackbarContext.Provider value={displaySnackbar}>
      {children}
      <Closable open={isOpenSnackbar()}>
        <Snackbar
          open={isOpenSnackbar()}
          autoHideDuration={5000}
          message={message}
          onClose={handleCloseSnackbar}
        />
      </Closable>
    </SnackbarContext.Provider>
  );
};
